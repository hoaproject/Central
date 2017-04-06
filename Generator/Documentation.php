<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Hoa\Test\Generator;

use Hoa\File;
use League\CommonMark;
use ReflectionClass;

/**
 * Class \Hoa\Test\Generator\Documentation.
 *
 * Functions to parse API documentations and compile them into integration
 * tests.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Documentation
{
    const DOCUMENTATION_SECTION = 'Examples';

    const NO_STATE                  = 0;
    const STATE_USE                 = 1;
    const STATE_NAME                = 2;
    const STATE_NAMESPACE_SEPARATOR = 3;

    protected $_directoryToScan = null;

    protected $_namespaceToScan = null;



    public function generate($directoryToScan, $namespaceToScan)
    {
        $this->_directoryToScan = $directoryToScan;
        $this->_namespaceToScan = $namespaceToScan;

        $this->includeAutoloader();

        $reflectedClasses = $this->getClassesToCompile();
        $testSuites       = $this->compileToTestSuites($reflectedClasses);

        $this->saveToFile($testSuites);

        return;
    }

    protected function getFinder($directoryToScan)
    {
        $finder = new File\Finder();
        $finder
            ->files()
            ->name('/\.php$/')
            ->in($directoryToScan)
            ->notIn('/^\.git|vendor|Test$/');

        $documentationDirectory =
            $directoryToScan . DS .
            'Test' . DS .
            'Documentation';

        if (is_dir($documentationDirectory)) {
            $since = time() - filemtime($documentationDirectory);
            $finder->modified('since ' . $since . ' seconds');
        }

        return $finder;
    }

    protected function includeAutoloader()
    {
        $autoloaderPath = $this->_directoryToScan . DS . 'vendor' . DS . 'autoload.php';

        if (false === file_exists($autoloaderPath)) {
            return;
        }

        require_once $autoloaderPath;

        return;
    }

    protected function getClassesToCompile()
    {
        $classes = get_declared_classes();
        $finder  = $this->getFinder($this->_directoryToScan);

        foreach ($finder as $file) {
            require_once $file->getPathName();
        }

        $reflectedClasses = [];
        $regex            = '/^' . preg_quote($this->_namespaceToScan, '/') . '/';

        foreach (array_diff(get_declared_classes(), $classes) as $class) {
            if (0 === preg_match($regex, $class)) {
                continue;
            }

            $reflectedClasses[] = new ReflectionClass($class);
        }

        return $reflectedClasses;
    }

    protected function getMarkdownParser()
    {
        return new CommonMark\DocParser(CommonMark\Environment::createCommonMarkEnvironment());
    }

    protected function extractFromComment($comment)
    {
        return preg_replace(
            ',^(/\*\*|\h*\*/?\h*),m',
            '',
            $comment
        );
    }

    protected function unfoldCode($code)
    {
        // Remove `#`, and add an opening tag (for the lexer).
        $code =
            '<?php ' .
            preg_replace(
                ',^\h*(#\h*),m',
                '',
                trim($code)
            );

        // Contains an array of the form: `string -> string` where the key is
        // the string to search, and the value is the string to use to replace
        // the search.
        $aliases  = [];

        // Current state. See `self::NO_STATE` and `self::STATE_*` constants.
        $state    = self::NO_STATE;

        // Small buffer. Not related to the accumulator. It will store an alias.
        $buffer   = null;

        return array_reduce(
            token_get_all($code),
            function ($accumulator, $token) use (&$buffer, &$state, &$aliases) {
                // If the token is not an array…
                if (!is_array($token)) {
                    $tokenValue = $token;

                    // If the end of a `use` statement is being read, then it
                    // is possible to add a new alias in the `$aliases` array.
                    if (self::STATE_USE === $state && ';' === $tokenValue) {
                        $replace = '\\' . $buffer;

                        if (false !== $pos = strrpos($buffer, '\\')) {
                            $search = substr($buffer, $pos + 1);
                        } else {
                            $search = $buffer;
                        }

                        $aliases[$search] = $replace;
                        $state            = self::NO_STATE;
                        $buffer           = null;
                    }

                    return $accumulator . $tokenValue;
                }

                list($tokenType, $tokenValue) = $token;

                // Skip the opening tag.
                if (T_OPEN_TAG === $tokenType) {
                    return $accumulator;
                }
                // Enter a `use` statement.
                else if (T_USE === $tokenType) {
                    $state      = self::STATE_USE;
                    $tokenValue = '# ' . $tokenValue;
                }
                // Reading a `use` statement.
                else if (self::STATE_USE === $state) {
                    if (T_STRING === $tokenType || T_NS_SEPARATOR === $tokenType) {
                        $buffer .= $tokenValue;
                    }
                }
                // A namespace separator is being read. Change the state to
                // `STATE_NAMESPACE_SEPARATOR`. This way, the next `T_STRING`
                // will be ignored when unfolded aliases.
                else if (T_NS_SEPARATOR === $tokenType) {
                    $state = self::STATE_NAMESPACE_SEPARATOR;
                }
                // A name is being read.
                else if (T_STRING === $tokenType) {
                    // But the previous significant token is a namespace
                    // separator, so skip it.
                    if (self::STATE_NAMESPACE_SEPARATOR === $state) {
                        $state = self::NO_STATE;
                    }
                    // Great, we can replace this name —which is an alias— by
                    // its fully-qualified name.
                    else if (self::NO_STATE === $state && isset($aliases[$tokenValue])) {
                        $tokenValue = $aliases[$tokenValue];
                    }
                }

                return $accumulator . $tokenValue;
            },
            ''
        );
    }

    protected function collectCodeBlocks(CommonMark\Node\NodeWalker $walker)
    {
        while ($event = $walker->next()) {
            $node = $event->getNode();

            if (false === $event->isEntering() ||
                !($node instanceof CommonMark\Block\Element\Heading) ||
                1 !== $node->getLevel() ||
                self::DOCUMENTATION_SECTION !== $node->getStringContent()) {
                continue;
            }

            $codeBlocks = [];

            while ($childEvent = $walker->next()) {
                $childNode = $childEvent->getNode();

                if ($childNode instanceof CommonMark\Block\Element\Heading &&
                    self::DOCUMENTATION_SECTION !== $childNode->getStringContent()) {
                    break;
                }

                if (false === $event->isEntering() ||
                    !($childNode instanceof CommonMark\Block\Element\FencedCode)) {
                    continue;
                }

                $hash = spl_object_hash($childNode);

                if (isset($codeBlocks[$hash])) {
                    continue;
                }

                $type = trim($childNode->getInfo());

                if (empty($type)) {
                    $type = 'php';
                }

                if (0 === preg_match('/\bphp\b/', $type)) {
                    continue;
                }

                $code = $this->unfoldCode($childNode->getStringContent());

                $codeBlock = [
                    'type' => $type,
                    'code' => $code
                ];

                $codeBlocks[$hash] = $codeBlock;
            }

            return $codeBlocks;
        }

        return [];
    }

    protected function compileToTestCaseBody(array $codeBlock)
    {
        if (0 !== preg_match('/\bignore\b/', $codeBlock['type'])) {
            return sprintf(
                '        $this' . "\n" .
                '            ->skip(\'Skipped because ' .
                'the code block type contains `ignore`: `%s`.\');',
                $codeBlock['type']
            );
        }


        if (0 !== preg_match('/\bmust_throw(?:\(([^\)]+)\)|\b)/', $codeBlock['type'], $matches)) {
            return sprintf(
                '        $this' . "\n" .
                '            ->exception(function () {' . "\n" .
                '                %s' . "\n" .
                '            })' . "\n" .
                '                ->isInstanceOf(\'%s\');',
                preg_replace(
                    '/^\h+$/m',
                    '',
                    str_replace("\n", "\n" . '                ', $codeBlock['code'])
                ),
                isset($matches[1]) ? $matches[1] : 'Exception'
            );
        }

        return sprintf(
            '        $this' . "\n" .
            '            ->assert(function () {' . "\n" .
            '                %s' . "\n" .
            '            });',
            preg_replace(
                '/^\h+$/m',
                '',
                str_replace("\n", "\n" . '                ', $codeBlock['code'])
            )
        );
    }

    protected function compileToTestSuites(array $reflectedClasses)
    {
        $testSuites = [];
        $markdown   = $this->getMarkdownParser();

        foreach ($reflectedClasses as $reflectedClass) {
            list($namespace1, $namespace2, $namespaceN) = array_merge(explode('\\', $reflectedClass->getNamespaceName(), 3), [2 => null]);

            $testSuite = sprintf(
                '<?php' . "\n\n" .
                'namespace %s\%s\Test\Documentation%s;' . "\n\n" .
                'use Hoa\Test;' . "\n\n" .
                'class %s extends Test\Integration\Suite' . "\n" .
                '{',
                $namespace1,
                $namespace2,
                !empty($namespaceN) ? '\\' . $namespaceN : '',
                $reflectedClass->getShortName()
            );
            $outputFile = sprintf(
                '%s/Test/Documentation%s/%s.php',
                $this->_directoryToScan,
                !empty($namespaceN) ? '/' . $namespaceN : '',
                $reflectedClass->getShortName()
            );

            $anyTestCase = false;

            foreach ($reflectedClass->getMethods() as $reflectedMethod) {
                $comment = $reflectedMethod->getDocComment();

                if (false === $comment) {
                    continue;
                }

                $comment    = $this->extractFromComment($comment);
                $codeBlocks = $this->collectCodeBlocks($markdown->parse($comment)->walker());

                if (empty($codeBlocks)) {
                    continue;
                }

                $anyTestCase   = true;
                $testCaseIndex = 0;

                foreach ($codeBlocks as $codeBlock) {
                    $testSuite .= sprintf(
                        "\n" .
                        '    public function case_%s_example_%d()' . "\n" .
                        '    {' . "\n" .
                        '%s' . "\n" .
                        '    }' . "\n",
                        $reflectedMethod->getName(),
                        $testCaseIndex,
                        $this->compileToTestCaseBody($codeBlock)
                    );

                    $testCaseIndex++;
                }
            }

            $testSuite .= '}';

            if (true === $anyTestCase) {
                $testSuites[$reflectedClass->getName()] = [
                    'code'       => $testSuite,
                    'outputFile' => $outputFile
                ];
            }
        }

        return $testSuites;
    }

    protected function saveToFile(array $testSuites)
    {
        foreach ($testSuites as $testSuite) {
            $code       = $testSuite['code'];
            $outputFile = $testSuite['outputFile'];

            $outputDirectory = dirname($outputFile);

            if (false === is_dir($outputDirectory)) {
                mkdir($outputDirectory, 0777, true);
            }

            file_put_contents(
                $outputFile,
                $code
            );
        }

        // Update the mtime of the `Documentation` for the caching algorithm.
        touch($this->_directoryToScan . DS . 'Test' . DS . 'Documentation');

        return;
    }
}

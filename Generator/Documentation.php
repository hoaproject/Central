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

    protected $_directoryToScan = null;

    protected $_namespaceToScan = null;



    public function __construct($directoryToScan, $namespaceToScan)
    {
        $this->_directoryToScan = $directoryToScan;
        $this->_namespaceToScan = $namespaceToScan;

        return;
    }

    public function generate()
    {
        $this->includeAutoloader();

        $reflectedClasses = $this->getClassesToCompile();
        $testSuites       = $this->compileToTestSuites($reflectedClasses);

        $this->saveToFile($testSuites);
    }

    protected function getFinder($directoryToScan)
    {
        $finder = new File\Finder();
        $finder
            ->files()
            ->name('/\.php$/')
            ->in($directoryToScan)
            ->notIn('/^\.git|vendor|Test$/');

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

                $codeBlock = [
                    'type' => $childNode->getInfo(),
                    'code' => trim($childNode->getStringContent())
                ];

                $codeBlocks[$hash] = $codeBlock;
            }

            return $codeBlocks;
        }

        return [];
    }

    protected function compileToTestCaseBody(array $codeBlock)
    {
        return sprintf(
            '        $this' . "\n" .
            '            ->assert(function () {' . "\n" .
            '                %s' . "\n" .
            '            });' . "\n",
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
                'namespace %s\%s\Test\Integration%s;' . "\n\n" .
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
    }
}

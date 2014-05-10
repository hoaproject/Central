<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2014, Ivan Enderlin. All rights reserved.
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

namespace {

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '.autoload.atoum.php';

from('Hoa')

/**
 * \Hoa\Console\Processus
 */
-> import('Console.Processus')

/**
 * \Hoa\Console\Cursor
 */
-> import('Console.Cursor')

/**
 * \Hoa\String\Search
 */
-> import('String.Search');

from('Atoum')

/**
 * \Atoum\PraspelExtension\Praspel\Generator
 */
-> import('PraspelExtension.Praspel.Generator');

}

namespace Hoa\Test\Bin {

/**
 * Class Hoa\Test\Bin\Generate.
 *
 * Automatically generate test suites.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin.
 * @license    New BSD License
 */

class Generate extends \Hoa\Console\Dispatcher\Kit {

    /**
     * Options description.
     *
     * @var \Hoa\Test\Bin\Generate array
     */
    protected $options = array(
        array('classes', \Hoa\Console\GetOption::REQUIRED_ARGUMENT, 'c'),
        array('dry-run', \Hoa\Console\GetOption::NO_ARGUMENT,       'd'),
        array('help',    \Hoa\Console\GetOption::NO_ARGUMENT,       'h'),
        array('help',    \Hoa\Console\GetOption::NO_ARGUMENT,       '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $dryRun  = false;
        $classes = array();

        while(false !== $c = $this->getOption($v)) switch($c) {

            case 'c':
                foreach($this->parser->parseSpecialValue($v) as $class)
                    $classes[] = $class;
              break;

            case 'd':
                $dryRun = $v;
              break;

            case '__ambiguous':
                $this->resolveOptionAmbiguity($v);
              break;

            case 'h':
            case '?':
            default:
                return $this->usage();
              break;
        }

        if(empty($classes))
            return $this->usage();

        foreach($classes as $i => $class)
            $classes[$i] = str_replace('.', '\\', $class);

        $generator = new \Atoum\PraspelExtension\Praspel\Generator();
        $generator->setTestNamespacer(function ( $namespace ) {

            $parts = explode('\\', $namespace);

            return implode('\\', array_slice($parts, 0, 2)) .
                   '\\Test\\Praspel\\Unit' .
                   (isset($parts[2])
                       ? '\\' . implode('\\', array_slice($parts, 2))
                       : '');
        });

        $phpBinary = \Hoa\Core::getPHPBinary()
                         ?: \Hoa\Console\Processus::localte('php');

        $envVariable   = '__HOA_ATOUM_PRASPEL_EXTENSION_' . md5(\Hoa\Core::uuid());
        $reflection    = null;
        $buffer        = null;
        $reflectionner = new \Hoa\Console\Processus($phpBinary);
        $reflectionner->on('input', function ( \Hoa\Core\Event\Bucket $bucket )
                                         use ( $envVariable ) {

            $bucket->getSource()->writeAll(
                '<?php' . "\n" .
                'require_once \'' . dirname(__DIR__) . DS . '.bootstrap.atoum.php\';' . "\n" .
                '$class = getenv(\'' . $envVariable . '\');' . "\n" .
                'if(class_exists(\'\mageekguy\atoum\scripts\runner\', false))' . "\n" .
                '    \atoum\scripts\runner::disableAutorun();' . "\n" .
                '$reflection = new \Atoum\PraspelExtension\Praspel\Reflection\RClass($class);' . "\n" .
                'echo serialize($reflection), "\n";'
            );

            return false;
        });
        $reflectionner->on('output', function ( \Hoa\Core\Event\Bucket $bucket )
                                     use ( &$buffer ) {

            $data    = $bucket->getData();
            $buffer .= $data['line'] . "\n";

            return;
        });
        $reflectionner->on('stop', function ( ) use ( &$buffer, &$reflection ) {

            $handle = @unserialize($buffer);

            if(false === $handle) {

                echo $buffer, "\n";

                return;
            }

            $reflection = $handle;

            return;
        });

        foreach($classes as $class) {

            $status = $class . ' (in ';
            echo '  ⌛ ' , $status;

            putenv($envVariable . '=' . $class);
            $buffer     = null;
            $reflection = null;
            $reflectionner->run();
            $output = $generator->generate($reflection);

            $parts = explode('\\', $class);
            $paths = resolve(
                'hoa://Library/' .
                $parts[1] . '/' .
                'Test/Praspel/Unit/' .
                implode(
                    '/',
                    array_slice($parts, 2)
                ) .
                '.php',
                false,
                true
            );

            $max     = 0;
            $thePath = 0;

            foreach($paths as $path) {

                $length = \Hoa\String\Search::lcp(
                    $reflection->getFilename(),
                    $path
                );

                if($length > $max)
                    $thePath = $path;
            }

            $statusTail = (40 < strlen($thePath)
                               ? '…' . substr($thePath, -39)
                               : $thePath) . ')';
            echo $statusTail;
            $status .= $statusTail;

            $dirname = dirname($thePath);

            if(false === is_dir($dirname))
                if(false === $dryRun)
                    mkdir($dirname, 0755, true);
                else
                    echo "\n",
                         static::info('Creating directory: ' . $dirname . '.'),
                         "\n";

            if(false === $dryRun)
                file_put_contents($thePath, $output);
            else {

                echo static::info('Content of the ' . $thePath . ':'), "\n";
                \Hoa\Console\Cursor::colorize('foreground(yellow)');
                echo '    ┏', "\n",
                     '    ┃  ' ,
                     str_replace(
                        "\n",
                        "\n" . '    ┃  ',
                        trim($output)
                     ),
                     "\n",
                     '    ┗', "\n";
                \Hoa\Console\Cursor::colorize('foreground(normal)');
            }

            \Hoa\Console\Cursor::clear('↔');
            echo '  ', \Hoa\Console\Chrome\Text::colorize('✔︎', 'foreground(green)'),
                 ' ', $status, "\n";
        }

        return;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        echo 'Usage   : test:generate <options>', "\n",
             'Options :', "\n",
             $this->makeUsageOptionsList(array(
                 'c'    => 'Generate tests of some classes.',
                 'd'    => 'Generate tests but output them instead of save ' .
                           'them.',
                 'help' => 'This help.'
             )), "\n";

        return;
    }

    /**
     * Format a message for the dry-run mode.
     *
     * @access  protected
     * @param   string  $message    Message.
     * @param   bool    $sub        Whether this is a sub-message or not.
     * @return  string
     */
    protected static function info ( $message, $sub = false ) {

        return \Hoa\Console\Chrome\Text::colorize(
                  (false === $sub ? '# ' : '') . $message,
                  'foreground(yellow)'
               );
    }
}

}

__halt_compiler();
Automatically generate test suites.

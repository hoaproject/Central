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

}

namespace Hoa\Test\Bin {

/**
 * Class Hoa\Test\Bin\Generate.
 *
 * Compile Praspel test suite into atoum test suite.
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
        array('class', \Hoa\Console\GetOption::REQUIRED_ARGUMENT, 'c'),
        array('help',  \Hoa\Console\GetOption::NO_ARGUMENT,       'h'),
        array('help',  \Hoa\Console\GetOption::NO_ARGUMENT,       '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $classes = array();

        while(false !== $c = $this->getOption($v)) switch($c) {

            case 'c':
                $classes = array_merge(
                    $classes,
                    $this->parser->parseSpecialValue($v)
                );
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

        foreach($classes as &$class)
            $class = str_replace('.', '\\', $class);

        $generator = new \Atoum\PraspelExtension\Praspel\Generator();
        $generator->setTestNamespace('Test\\Praspel\\Unit');
        $generator->setTestNamespaceFormat('%2$s\\%1$s');

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

            putenv($envVariable . '=' . $class);
            $buffer = null;
            $reflectionner->run();

            $namespaceRoot = implode(
                '\\',
                array_slice(
                    explode('\\', $class),
                    0,
                    2 // vendor + library name.
                )
            );

            $output = $generator->generate($reflection);

            echo $output;
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
                 'c'    => 'Class to scan (. is replaced by \\).',
                 'help' => 'This help.'
             )), "\n";

        return;
    }
}

}

__halt_compiler();
Compile Praspel test suite into atoum test suite.

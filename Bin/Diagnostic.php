<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
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

namespace Hoa\Core\Bin {

    /**
     * Class \Hoa\Core\Bin\Diagnostic.
     *
     * This command generate an INI file for effecient debug.
     *
     * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
     * @author     Julien Clauzel <julien.clauzel@hoa-project.net>
     * @copyright  Copyright © 2007-2012 Ivan Enderlin, Julien Clauzel.
     * @license    New BSD License
     */

    class Diagnostic extends \Hoa\Console\Dispatcher\Kit
    {


        /**
         * Options description.
         *
         * @var array
         */
        protected $options = array(
            array('help', \Hoa\Console\GetOption::NO_ARGUMENT, 'h'),
            array('section', \Hoa\Console\GetOption::REQUIRED_ARGUMENT, 's'),
            array('help', \Hoa\Console\GetOption::NO_ARGUMENT, '?')

        );


        /**
         * The entry method.
         *
         * @access  public
         * @return  int
         */
        public function main()
        {

            $display = array();
            $diagnostic = array();


            while (false !== $c = $this->getOption($v)) switch ($c) {

                case 's':
                    $display =  $this->parser->parseSpecialValue ($v);
                    break;
                case 'h':
                case '?':
                    return $this->usage();
                    break;
                case '__ambiguous':
                    $this->resolveOptionAmbiguity($v);
                    break;
            }
            $constant = function ($constantName) {
                if (defined($constantName))
                    return constant($constantName);
                else
                    return 'constant ' . $constantName . ' is not defined !';
            };
  
            $store = function ($section, $key, $value = null) use (&$diagnostic) {
                if (is_array($key) && $value === null) {
                    foreach ($key as $i => $name)
                        $diagnostic[$section][$i] = $name;
                } else {
                    $diagnostic[$section][$key] = $value;
                }

            };
            // VERSION

            $store('version', 'hoa', HOA_VERSION_MAJOR . '.' . HOA_VERSION_MINOR . '.' . HOA_VERSION_RELEASE . HOA_VERSION_STATUS . (null !== HOA_VERSION_EXTRA ? '-' . HOA_VERSION_EXTRA : ''));
            $store('version', 'php', phpversion());
            $store('version', 'zend_engine', zend_version());
            $store('version', 'php_os', $constant('PHP_OS'));
            $store('version', 'plateform', php_uname());
	    $store('version' , 'architecture' , (S_32_BITS === true)? '32 BITS' : '64 BITS');

            // LOCATED File
            $core = \Hoa\Core\Core::getInstance();
            $hoa = $core->getParameters()->getFormattedParameter('root.hoa');

            $store('bin', 'hoa', $hoa);
            $store('bin', 'php', $constant('PHP_BINARY'));

            // LOADED EXTENSION
            $extension = get_loaded_extensions();

            foreach ($extension as $ext) {

                $version = new \ReflectionExtension($ext);
                $store('extension', $ext . '.version', $version->getVersion());
                    foreach ($version->getINIEntries() as $k => $v)
                        $store('extension', $k, $v);

            }


            // Configuration file
            $store('php.ini', ini_get_all(null, false));


            if (empty($display) or in_array('all' , $display))
                $ini = $this->arrayToIni($diagnostic);
            else {
                $t = array();
                foreach ($display as $d)
                    if (array_key_exists($d, $diagnostic))
                        $t[$d] = $diagnostic[$d];

                $ini = $this->arrayToIni($t);
            }

            echo $ini;
            return;
        }

        /**
         * The command usage.
         *
         * @access  public
         * @return  int
         */
        public function usage()
        {
            cout('Usage   : core:diagnostic <options>');
            cout('Options :');
            cout($this->makeUsageOptionsList(array(
                'help' => 'This help.',
                'section' => 'Display select section , "version" or "version,bin,php.ini" by default all is select'
            )));

            return;
        }


        /**
         * Transform an array into an INI String
         *
         * @param array $array
         * @return string
         */
        private function arrayToIni(Array $array)
        {
            $buffer = '';
            foreach ($array as $section => $subArray) {
                $buffer .= '[' . $section . ']' . "\n";
                foreach ($subArray as $key => $value) {
                    if (is_array($value))
                        $value = implode(' ', $value);

                    $buffer .= $key . ' = "' . $value . '"' . "\n";
                }


            }
            return $buffer;
        }
    }

}

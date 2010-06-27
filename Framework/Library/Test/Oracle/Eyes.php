<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 * @category    Framework
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Oracle_Eyes
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Test_Oracle_Exception
 */
import('Test.Oracle.Exception');

/**
 * Hoa_Test_Praspel_Compiler
 */
import('Test.Praspel.Compiler');

/**
 * Class Hoa_Test_Oracle_Eyes.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Oracle_Eyes
 */

class Hoa_Test_Oracle_Eyes implements Hoa_Core_Parameterizable {

    /**
     * Parameters of Hoa_Test.
     *
     * @var Hoa_Core_Parameter object
     */
    protected $_parameters = null;



    /**
     * Set the parameters of this package from Hoa_Test.
     *
     * @access  public
     * @param   Hoa_Core_Parameter  $parameters    Parameters.
     * @return  Hoa_Test_Oracle
     */
    public function setRequest ( Hoa_Core_Parameter $parameters ) {

        $this->_parameters = $parameters;

        return $this;
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in      Parameters to set.
     * @return  void
     * @throw   Hoa_Exception
     */
    public function setParameters ( Array $in ) {

        return $this->_parameters->setParameters($this, $in);
    }

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Exception
     */
    public function getParameters ( ) {

        return $this->_parameters->getParameters($this);
    }

    /**
     * Set a parameter to a class.
     *
     * @access  public
     * @param   string  $key      Key.
     * @param   mixed   $value    Value.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function setParameter ( $key, $value ) {

        return $this->_parameters->setParameter($this, $key, $value);
    }

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   string  $key      Key.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getParameter ( $key ) {

        return $this->_parameters->getParameter($this, $key);
    }

    /**
     * Get a formatted parameter from a class (i.e. zFormat with keywords and
     * other parameters).
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getFormattedParameter ( $key ) {

        return $this->_parameters->getFormattedParameter($this, $key);
    }

    /**
     * Oracle opens eyes and looks the future.
     *
     * @access  public
     * @return  void
     */
    public function open ( ) {

        $incubator    = $this->getFormattedParameter('incubator');
        $battleground = $this->getFormattedParameter('ordeal.battleground');
        $files        = $this->getFormattedParameter('convict.result');
        $compiler     = new Hoa_Test_Praspel_Compiler();

        foreach($files as $e => $file) {

            if(   !is_dir(dirname($battleground . $file))
               && false === @mkdir(dirname($battleground . $file), 0777, true))
                throw new Hoa_Test_Oracle_Exception(
                    'Cannot create the ordeal.battleground in %s.',
                    0, dirname($battleground . $file));

            require_once $incubator . $file;

            preg_match_all(
                '#@class\s+([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*);#',
                $handle = file_get_contents($incubator . $file),
                $matches
            );

            $handle  = explode("\n", $handle);
            $classes = $matches[1];
            $out     = '<?php';
            $foot    = "\n\n" .
                       '$exportTests = array(' . "\n";

            foreach($classes as $i => $class) {

                $rClass   = new ReflectionClass($class);
                $methods  = $rClass->getMethods(
                    ReflectionMethod::IS_STATIC    |
                    ReflectionMethod::IS_PUBLIC    |
                    ReflectionMethod::IS_PROTECTED |
                    ReflectionMethod::IS_PRIVATE   |
                    ReflectionMethod::IS_FINAL
                );
                $out     .= "\n\n" .
                            '/**' . "\n" .
                            ' * Test class ' . $class . "\n" .
                            ' * in ' . $incubator . $file . ".\n" .
                            ' */' . "\n" .
                            'class Hoatest_' . $class . ' {' . "\n\n" .
                            '    /**' . "\n" .
                            '     * Memorize the instance.' . "\n" .
                            '     */' . "\n" .
                            '    public $_convict = null;' . "\n\n";
                $foot    .= '    \'' . $class . '\' => array(' . "\n";

                foreach($methods as $j => $method) {

                    $comment  = $method->getDocComment();
                    $comment  = preg_replace('#^(\s*/\*\*\s*)#', '', $comment);
                    $comment  = preg_replace('#(\s*\*/)$#',      '', $comment);
                    $comment  = preg_replace('#^(\s*\*\s*)#m',   '', $comment);

                    $compiler->compile($comment);

                    $praspel  = $compiler->getRoot()->__toString();
                    $praspel  = str_replace("\n", "\n        ", $praspel);
                    $out     .= "\n\n" .
                                '    /**' . "\n" .
                                '     * Test method ' . $class . '::' .
                                $method->getName() . "()\n" .
                                '     * in file ' . $incubator . $file . "\n" .
                                '     * from line ' . $method->getStartLine() .
                                ' to ' . $method->getEndLine() . ".\n" .
                                '     */' . "\n" .
                                '    public function __test_' . $method->getName() .
                                ' ( ) {' . "\n\n" .
                                '        $class     = \'' . $class . '\';' . "\n" .
                                '        $method    = \'' . $method->getName() . '\';' . "\n" .
                                '        $file      = \'' . $incubator . $file . '\';' . "\n" .
                                '        $startLine = ' . $method->getStartLine() . ";\n" .
                                '        $endLine   = ' . $method->getEndLine() . ";\n" .
                                '        ' . $praspel .
                                '$praspel->call(' . "\n" .
                                '            $this->_convict,' . "\n" .
                                '            \'__hoa_magicCaller\',' . "\n" .
                                '            \'' . $class . '\',' . "\n" .
                                '            \'' . $method->getName() . '\'' . "\n" .
                                '        );' . "\n" .
                                '        $praspel->verify();' . "\n" .
                                '    }';
                    $foot    .= '        ' . $j . ' => \'' . $method->getName() . '\',' . "\n";
                }

                $out     .= "\n" . '}';
                $foot    .= '    ),' . "\n";
            }

            $foot .= ');';

            file_put_contents($battleground . $file, $out . $foot);
        }

        return;
    }
}

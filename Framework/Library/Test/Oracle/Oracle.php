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
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Test_Oracle
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Test_Oracle_Exception
 */
import('Test.Oracle.Exception');

/**
 * Hoa_Test_Oracle_Eyes
 */
import('Test.Oracle.Eyes');

/**
 * Class Hoa_Test_Oracle.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Oracle
 */

class Hoa_Test_Oracle implements Hoa_Framework_Parameterizable {

    /**
     * Parameters of Hoa_Test.
     *
     * @var Hoa_Framework_Parameter object
     */
    protected $_parameters = null;



    /**
     * Set the parameters of this package from Hoa_Test.
     *
     * @access  public
     * @param   Hoa_Framework_Parameter  $parameters    Parameters.
     * @return  Hoa_Test_Oracle
     */
    public function setRequest ( Hoa_Framework_Parameter $parameters ) {

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
     * Ask the oracle the predict future.
     *
     * @access  public
     * @return  void
     */
    public function predict ( ) {

        $this->prepareIncubator();
        $this->prepareOrdealOracle();
        $this->prepareOrdealBattleground();
        $this->prepareEyes();

        return;
    }

    /**
     * Prepare the incubator.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Test_Oracle_Exception
     */
    protected function prepareIncubator ( ) {

        $convict   = $this->getFormattedParameter('convict.directory');
        $recursive = $this->getFormattedParameter('convict.recursive');
        $incubator = $this->getFormattedParameter('incubator');

        if(null === $convict)
            throw new Hoa_Test_Oracle_Exception(
                'A file or a directory must be specified to run tests.', 0);

        $files = array();

        if(is_dir($convict)) {

            $iterator = new RecursiveDirectoryIterator($convict);

            if(true === $recursive)
                $iterator = new RecursiveIteratorIterator(
                                $iterator,
                                RecursiveIteratorIterator::SELF_FIRST
                            );

            $strlen = strlen($convict);

            foreach($iterator as $name => $splFileInfo) {

                if(substr($name, -4) != '.php')
                    continue;

                $files[] = substr($name, $strlen);
            }
        }
        elseif(is_file($convict))
            $files[] = basename($convict);
        else
            throw new Hoa_Test_Oracle_Exception(
                '%s is not a valid file or directory.', 1, $convict);

        if(is_dir($incubator)) {

            foreach(new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($incubator),
                        RecursiveIteratorIterator::CHILD_FIRST
                    ) as $name => $splFileInfo)
                if(is_dir($name))
                    rmdir($name);
                elseif(is_file($name))
                    unlink($name);

            rmdir($incubator);
        }

        if(false === mkdir($incubator, 0777, true))
            throw new Hoa_Test_Oracle_Exception(
                'Cannot create the incubator in %s.', 2, $incubator);

        foreach($files as $i => $file) {

            if(!is_dir(dirname($incubator . $file)))
                mkdir(dirname($incubator . $file), 0777, true);

            if(false === @copy($convict . $file, $incubator . $file))
                throw new Hoa_Test_Oracle_Exception(
                    'Cannot copy %s in %s.', 3,
                    array($convict . $file, $incubator . $file));
        }

        $this->setParameter('convict.result', $files);

        return;
    }

    /**
     * Prepare the ordeal.oracle.
     *
     * @access  protected
     * @return  void
     * @throw   Hoa_Test_Oracle_Exception
     */
    protected function prepareOrdealOracle ( ) {

        $convict   = $this->getFormattedParameter('convict.result');
        $incubator = $this->getFormattedParameter('incubator');
        $oracle    = $this->getFormattedParameter('ordeal.oracle');
        $prefix    = $this->getFormattedParameter('oracle.eyes.methodPrefix');

        if(null === $oracle)
            throw new Hoa_Test_Oracle_Exception(
                'A directory for ordeal.oracle must be specified.', 4);

        if(is_dir($oracle)) {

            foreach(new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($oracle),
                        RecursiveIteratorIterator::CHILD_FIRST
                    ) as $name => $splFileInfo) {

                if(is_dir($name))
                    rmdir($name);
                elseif(is_file($name))
                    unlink($name);
            }

            rmdir($oracle);
        }

        if(false === @mkdir($oracle, 0777, true))
            throw new Hoa_Test_Oracle_Exception(
                'Cannot create the ordeal.oracle in %s.', 5, $oracle);

        $magic = explode(
                     "\n",
                     '    public function ' . $prefix . 'magicSetter ( ' .
                     '$attribut, $value ) {' . "\n\n" .
                     '        $old             = $this->$attribut;' . "\n" .
                     '        $this->$attribut = $value;' . "\n\n" .
                     '        return $old;' . "\n" .
                     '    }' . "\n\n" .
                     '    public function ' . $prefix . 'magicGetter ( ' .
                     '$attribut ) {' . "\n\n" .
                     '        return $this->$attribut;' . "\n" .
                     '    }' . "\n\n" .
                     '    public function ' . $prefix . 'magicCaller ( ' .
                     '$method ) {' . "\n\n" .
                     '        $arguments = func_get_args();' . "\n" .
                     '        array_shift($arguments);' . "\n" .
                     '        return call_user_func_array(' . "\n" .
                     '            array($this, $method),' . "\n" .
                     '            $arguments' . "\n" .
                     '        );' . "\n" .
                     '    }' . "\n" .
                     '}'
                 );

        foreach($convict as $e => $file) {

            if(   !is_dir(dirname($oracle . $file))
               && false === @mkdir(dirname($oracle . $file), 0777, true))
                throw new Hoa_Test_Oracle_Exception(
                    'Cannot create the ordeal.oracle in %s.',
                    6, dirname($oracle . $file));

            require_once $incubator . $file;

            preg_match_all(
                '#@class\s+([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*);#',
                $handle = file_get_contents($incubator . $file),
                $matches
            );

            $handle  = explode("\n", $handle);
            $classes = $matches[1];

            if(empty($classes))
                throw new Hoa_Test_Oracle_Exception(
                    'Classes in %s is not marked for testing. ' .
                    'Maybe you forget to add the @class tag.',
                    42, $incubator . $file);

            foreach($classes as $i => $class) {

                $c    = new ReflectionClass($class);
                $line = $handle[$c->getEndLine() - 1];

                for($end = strlen($line) - 1; $line[$end] != '}'; $end--);

                $line[$end]                   = ' ';
                $handle[$c->getEndLine() - 1] = $line;

                array_splice(
                    $handle,
                    $c->getEndLine(),
                    0,
                    $magic
                );
            }

            file_put_contents($oracle . $file, implode("\n", $handle));
        }

        return;
    }

    /**
     * Prepare the ordeal.battleground.
     *
     * @access  protected
     * @return  void
     * @throw   Hoa_Test_Oracle_Exception
     */
    protected function prepareOrdealBattleground ( ) {

        $battleground = $this->getFormattedParameter('ordeal.battleground');

        if(null === $battleground)
            throw new Hoa_Test_Oracle_Exception(
                'A directory for ordeal.battleground must be specified.', 7);

        if(is_dir($battleground)) {

            foreach(new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($battleground),
                        RecursiveIteratorIterator::CHILD_FIRST
                    ) as $name => $splFileInfo) {

                if(is_dir($name))
                    rmdir($name);
                elseif(is_file($name))
                    unlink($name);
            }

            rmdir($battleground);
        }

        if(false === @mkdir($battleground, 0777, true))
            throw new Hoa_Test_Oracle_Exception(
                'Cannot create the ordeal.battleground in %s.', 8, $battleground);

        return;
    }

    /**
     * Prepare eyes of oracle.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Test_Oracle_Exception
     */
    protected function prepareEyes ( ) {

        $eyes = new Hoa_Test_Oracle_Eyes();
        $this->_parameters->shareWith(
            $this,
            $eyes,
            Hoa_Framework_Parameter::PERMISSION_READ |
            Hoa_Framework_Parameter::PERMISSION_WRITE
        );
        $eyes->setRequest($this->_parameters);
        $eyes->open();
    }
}

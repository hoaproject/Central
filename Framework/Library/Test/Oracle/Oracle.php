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
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Hoa_Pom_Token_Class_Method
 */
import('Pom.Token.Class.Method');

/**
 * Hoa_Pom_Token_Call_Attribute
 */
import('Pom.Token.Call.Attribute');

/**
 * Hoa_Pom_Token_Call_Function
 */
import('Pom.Token.Call.Function');

/**
 * Hoa_Pom_Token_Instruction
 */
import('Pom.Token.Instruction');

/**
 * Hoa_Pom_Token_Operation
 */
import('Pom.Token.Operation');

/**
 * Hoa_Pom_Token_Operator_Assignement
 */
import('Pom.Token.Operator.Assignement');

/**
 * Hoa_Pom_Token_ControlStructure_Return
 */
import('Pom.Token.ControlStructure.Return');

/**
 * Hoa_Pom_Token_Array
 */
import('Pom.Token.Array');

/**
 * Class Hoa_Test_Oracle.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
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
        $incubator = $this->getFormattedParameter('test.incubator');

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
        $incubator = $this->getFormattedParameter('test.incubator');
        $oracle    = $this->getFormattedParameter('test.ordeal.oracle');
        $prefix    = $this->getFormattedParameter('test.ordeal.methodPrefix');

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

        $magicSetter = new Hoa_Pom_Token_Class_Method(
            new Hoa_Pom_Token_String($prefix . 'magicSetter')
        );
        $magicSetter->referenceMe(false);
        $magicSetter->addArguments(array(
            new Hoa_Pom_Token_Function_Argument(
                new Hoa_Pom_Token_Variable(
                    new Hoa_Pom_Token_String('attribut')
                )
            ),
            new Hoa_Pom_Token_Function_Argument(
                new Hoa_Pom_Token_Variable(
                    new Hoa_Pom_Token_String('value')
                )
            )
        ));
        $magicSetter->addBody(
            new Hoa_Pom_Token_Instruction(
                new Hoa_Pom_Token_Operation(array(
                    new Hoa_Pom_Token_Call_Attribute(
                        new Hoa_Pom_Token_Variable(
                            new Hoa_Pom_Token_String('this')
                        ),
                        new Hoa_Pom_Token_Variable(
                            new Hoa_Pom_Token_String('attribut')
                        )
                    ),
                    new Hoa_Pom_Token_Operator_Assignement('='),
                    new Hoa_Pom_Token_Variable(
                        new Hoa_Pom_Token_String('value')
                    )
                ))
            )
        );

        $magicGetter = new Hoa_Pom_Token_Class_Method(
            new Hoa_Pom_Token_String($prefix . 'magicGetter')
        );
        $magicGetter->referenceMe(false);
        $magicGetter->addArguments(array(
            new Hoa_Pom_Token_Function_Argument(
                new Hoa_Pom_Token_Variable(
                    new Hoa_Pom_Token_String('attribut')
                )
            )
        ));
        $magicGetter->addBody(
            new Hoa_Pom_Token_ControlStructure_Return(
                new Hoa_Pom_Token_Call_Attribute(
                    new Hoa_Pom_Token_Variable(
                        new Hoa_Pom_Token_String('this')
                    ),
                    new Hoa_Pom_Token_Variable(
                        new Hoa_Pom_Token_String('attribut')
                    )
                )
            )
        );

        $magicCaller = new Hoa_Pom_Token_Class_Method(
            new Hoa_Pom_Token_String($prefix . 'magicCaller')
        );
        $magicCaller->referenceMe(false);
        $magicCaller->addArguments(array(
            new Hoa_Pom_Token_Function_Argument(
                new Hoa_Pom_Token_Variable(
                    new Hoa_Pom_Token_String('method')
                )
            )
        ));
        $magicCaller->addBody(
            new Hoa_Pom_Token_Instruction(
                new Hoa_Pom_Token_Operation(array(
                    new Hoa_Pom_Token_Variable(
                        new Hoa_Pom_Token_String('arguments')
                    ),
                    new Hoa_Pom_Token_Operator_Assignement('='),
                    new Hoa_Pom_Token_Call_Function(
                        new Hoa_Pom_Token_String('func_get_args')
                    )
                ))
            )
        );
        $magicCaller->addBody(
            new Hoa_Pom_Token_Instruction(
                new Hoa_Pom_Token_Call_Function(
                    new Hoa_Pom_Token_String('array_shift'),
                    array(
                        new Hoa_Pom_Token_Variable(
                            new Hoa_Pom_Token_String('arguments')
                        )
                    )
                )
            )
        );
        $magicCaller->addBody(
            new Hoa_Pom_Token_ControlStructure_Return(
                new Hoa_Pom_Token_Call_Function(
                    new Hoa_Pom_Token_String('call_user_func_array'),
                    array(
                        new Hoa_Pom_Token_Array(array(
                            new Hoa_Pom_Token_Variable(
                                new Hoa_Pom_Token_String('this')
                            ),
                            new Hoa_Pom_Token_Variable(
                                new Hoa_Pom_Token_String('method')
                            )
                        )),
                        new Hoa_Pom_Token_Variable(
                            new Hoa_Pom_Token_String('arguments')
                        )
                    )
                )
            )
        );

        foreach($convict as $i => $file) {

            $root = Hoa_Pom::parse($incubator . $file, Hoa_Pom::TOKENIZE_FILE);

            foreach($root->getElements() as $i => $element)
                if($element instanceof Hoa_Pom_Token_Class)
                    $element->addMethods(array(
                        $magicSetter,
                        $magicGetter,
                        $magicCaller
                    ));

            file_put_contents($oracle . $file, Hoa_Pom::dump($root));
        }
    }

    /**
     * Prepare the ordeal.battleground.
     *
     * @access  protected
     * @return  void
     * @throw   Hoa_Test_Oracle_Exception
     */
    protected function prepareOrdealBattleground ( ) {

        $battleground = $this->getFormattedParameter('test.ordeal.battleground');

        if(null === $battleground)
            throw new Hoa_Test_Oracle_Exception(
                'A directory for ordeal.battleground must be specified.', 6);

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
                'Cannot create the ordeal.battleground in %s.', 7, $battleground);
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

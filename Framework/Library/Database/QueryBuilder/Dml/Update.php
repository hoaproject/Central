<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 *
 *
 * @category    Framework
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_QueryBuilder_Dml_Update
 *
 */

/**
 * Hoa_Database_QueryBuilder_Dml_Exception
 */
import('Database.QueryBuilder.Dml.Exception');

/**
 * Hoa_Database_QueryBuilder_Interface
 */
import('Database.QueryBuilder.Interface');

/**
 * Class Hoa_Database_QueryBuilder_Dml_Update.
 *
 * Build UPDATE query.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright © 2007-2011 Ivan ENDERLIN.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_QueryBuilder_Dml_Update
 */

class Hoa_Database_QueryBuilder_Dml_Update implements Hoa_Database_QueryBuilder_Interface {

    /**
     * The built query.
     *
     * @var Hoa_Database_QueryBuilder_Dml_Update string
     */
    protected $query = null;

    /**
     * The table.
     *
     * @var Hoa_Database_Model_Table object
     */
    protected $_table = null;



    /**
     * Set the table and built the query.
     *
     * @access  public
     * @param   Hoa_Database_Model_Table  $table     The table.
     * @param   array                     $values    Array that represents keys
     *                                               and values to update.
     * @return  void
     */
    public function __construct ( Hoa_Database_Model_Table $table,
                                  Array                    $values = array() ) {

        $this->setTable($table);
        $this->builtQuery($values);
    }

    /**
     * Set the table.
     *
     * @access  protected
     * @param   Hoa_Database_Model_Table  $table    The table.
     * @return  Hoa_Database_Model_Table
     */
    protected function setTable ( Hoa_Database_Model_Table $table ) {

        $old          = $this->_table;
        $this->_table = $table;

        return $old;
    }

    /**
     * Get the table.
     *
     * @access  public
     * @return  Hoa_Database_Model_Table
     */
    public function getTable ( ) {

        return $this->_table;
    }

    /**
     * Built the query.
     *
     * @access  protected
     * @param   array      $values    Array that represents key and values to
     *                                update.
     * @return  string
     */
    protected function builtQuery ( Array $values = array() ) {

        $query = array(
            'set'   => array(),
            'where' => array(),
        );

        if(empty($values)) {

            $this->query = null;

            return $this->getQuery();
        }

        $lastWhere = null;

        foreach($this->getTable() as $foo => $field) {

            if(true === array_key_exists($field->getName(), $values)) {

                $query['set'][] = $field->getName() .
                                  ' = ' .
                                  ':' . md5($field->getName() . $values[$field->getName()]);

                $this->getTable()->addPreparedValue(
                    md5($field->getName() . $values[$field->getName()]),
                    $values[$field->getName()]
                );
            }

            $tmp = trim($field->getWhereString());

            if(!empty($tmp)) {

                if(null !== $lastWhere) {

                    $lastWhere->getCriterion()->_and();
                    $query['where'][] = trim($lastWhere->getWhereString());
                }

                $lastWhere = $field;
            }
        }

        null !== $lastWhere and $query['where'][] = trim($lastWhere->getWhereString());

        $query['set']   = implode(",\n", $query['set']);
        $query['where'] = implode("\n",  $query['where']);

        $out = 'UPDATE ' . $this->getTable()->getName() . "\n" .
               'SET    ' . "\n" . str_repeat(' ', 7) .
               str_replace(
                   "\n",
                   "\n" . str_repeat(' ', 7),
                   trim($query['set'])
               );

        if(!empty($query['where']))
            $out .=
               "\n" .
               'WHERE  ' . str_replace(
                               "\n",
                               "\n" . str_repeat(' ', 7),
                               trim($query['where'])
                           );

        $this->query = trim($out);

        return $this->getQuery();
    }

    /**
     * Get the built query.
     *
     * @access  public
     * @return  string
     */
    public function getQuery ( ) {

        return $this->query;
    }

    /**
     * Call the self::getQuery() method.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return $this->getQuery();
    }
}

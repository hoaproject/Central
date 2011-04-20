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
 * @subpackage  Hoa_Database_QueryBuilder_Ddl_Create
 *
 */

/**
 * Hoa_Database
 */
import('Database.~');

/**
 * Hoa_Database_QueryBuilder_Ddl_Exception
 */
import('Database.QueryBuilder.Ddl.Exception');

/**
 * Hoa_Database_QueryBuilder_Ddl_Abstract
 */
import('Database.QueryBuilder.Ddl.Abstract');

/**
 * Class Hoa_Database_QueryBuilder_Ddl_Create.
 *
 * Build the CREATE TABLE instructions.
 *
 * @author      Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright © 2007-2011 Ivan Enderlin.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_QueryBuilder_Ddl_Create
 */

class Hoa_Database_QueryBuilder_Ddl_Create extends Hoa_Database_QueryBuilder_Ddl_Abstract {

    /**
     * Build the CREATE TABLE instructions.
     *
     * @access  protected
     * @return  string
     */
    protected function toStringTable ( ) {

        $_    = $this->get();
        $pt   = Hoa_Database::getInstance();
        $out  = 'CREATE TABLE ' . $_->getName() . " (\n";

        foreach($_ as $foo => $field) {

            $constraint = $field->getConstraint();

            $out .= '    ' . $field->getName();
            $out .= $constraint->isNull()
                        ? ' NULL'
                        : ' NOT NULL';
            $out .= ' ' . $constraint->getType();

            $out .= ",\n";
        }

        $out = substr($out, 0, -2);

        $tmp = array();
        foreach($_->getConstraints()->getPrimaries() as $foo => $field)
            $tmp[] = $field->getName();

        if(!empty($tmp))
            $out .= ",\n" .
                    '    CONSTRAINT ' .
                    $pt->getParameter(
                        'table.pkname',
                        $_->getName()
                     ) .
                     ' PRIMARY KEY (' . implode(', ', $tmp) . ')';

        $tmp = $_->getConstraints()->getForeigns();

        if(!empty($tmp)) {

            foreach($tmp as $foo => $field) {

                list($t, $f) = explode('.', $field->getConstraint()->getForeign());

                $out .= ",\n" .
                        '    CONSTRAINT ' .
                        $pt->getParameter(
                            'table.fkname',
                            $field->getName()
                        ) .
                        ' FOREIGN KEY (' . $field->getName() . ')' . "\n" .
                        '              ' .
                        ' REFERENCES ' . $t . ' (' . $f . ')';
            }
        }

        $out .= "\n" . ');';

        return $out;
    }

    /**
     * Call the self::toStringTable().
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        if(parent::isTable())
            return $this->toStringTable();

        return null;
    }
}

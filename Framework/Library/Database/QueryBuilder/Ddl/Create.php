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
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_QueryBuilder_Ddl_Create
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

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
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
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

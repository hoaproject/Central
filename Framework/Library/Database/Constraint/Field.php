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
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Constraint_Field
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Database_Constraint_Exception
 */
import('Database.Constraint.Exception');

/**
 * Hoa_Database_Constraint_Abstract
 */
import('Database.Constraint.Abstract');

/**
 * Class Hoa_Database_Constraint_Field.
 *
 * Manage field constraints.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Constraint_Field
 */

class Hoa_Database_Constraint_Field extends Hoa_Database_Constraint_Abstract {

    /**
     * Category type : litteral > 2 bits coding (ASCII) ; defines CHARACTER and
     * CHARACTER VARYING.
     *
     * @const int
     */
    const TYPE_STRING = 0;

    /**
     * Category type : litteral > 4 bits coding (UNICODE) ; defines NATIONAL
     * CHARACTER and NATIONAL CHARACTER VARYING.
     *
     * @const int
     */
    const TYPE_NATIONAL_STRING = 1;

    /**
     * Category type : numerical > real > exact ; defines DECIMAL and NUMERIC.
     *
     * @const int
     */
    const TYPE_EXACT = 2;

    /**
     * Category type : numerical > real > approched ; defines FLOAT, REAL, and
     * DOUBLE PRECISION.
     *
     * @const int
     */
    const TYPE_APPROCHED = 4;

    /**
     * Category type : numerical > integer ; defines SMALLINT and INTEGER.
     *
     * @const int
     */
    const TYPE_INTEGER = 8;

    /**
     * Category type : binary ; defines BIT and BIT VARYING.
     *
     * @const int
     */
    const TYPE_BINARY = 16;

    /**
     * Category type : temporal ; defines TIMESTAMP [WITH TIME ZONE] and
     * INTERVAL i_1 [p] [TO i_2].
     *
     * @const int
     */
    const TYPE_TEMPORAL = 32;

    /**
     * Category type : temporal > time ; defines DATE and TIME [WITH TIME
     * ZONE].
     *
     * @const int
     */
    const TYPE_DATETIME = 64;

    /**
     * Category type : boolean ; defines BOOLEAN.
     *
     * @const int
     */
    const TYPE_BOOLEAN = 128;

    /**
     * Category type : CLOB ; defines CHARACTER LARGE OBJECT and NATIONAL
     * CHARACTER LARGE OBJECT.
     *
     * @const int
     */
    const TYPE_CLOB = 256;

    /**
     * Category type : BLOB ; defines BINARY LARGE OBJECT.
     *
     * @const int
     */
    const TYPE_BLOB = 512;

    /**
     * Category type : structure ; defines ARRAY, ROW, and REF.
     *
     * @const int
     */
    const TYPE_STRUCTURE = 1024;

    /**
     * Category type : UDT (User Domain Type).
     *
     * @const int
     */
    const TYPE_UDT = 2048;

    /**
     * Constraints.
     *
     * @var Hoa_Database_Constraint_Abstract array
     */
    protected $constraints = array(
        'type'          => null,
        'category'      => null,
        'isNull'        => false,
        'default'       => null,
        'primary'       => false,
        'unique'        => false,
        'index'         => false,
        'foreign'       => null,
        'autoIncrement' => false,
        'comment'       => null,
        'check'         => null,
        'charSet'       => null,
        'charCollate'   => null
    );



    /**
     * Set the field when calling the parent constructor.
     * Set the category type according to the type.
     *
     * @access  public
     * @param   Hoa_Database_Model_Field  $field          The field that needs
     *                                                    constraints.
     * @param   array                     $constraints    The field constraints.
     * @return  void
     * @throw   Hoa_Database_Constraint_Exception
     */
    public function __construct ( Hoa_Database_Model_Field $field,
                                  Array                    $constraints = array() ) {

        parent::__construct($field, $constraints);
        $this->setCategoryType();
        $this->checkConstraints();
    }

    /**
     * Set the category type automatically according to the type.
     *
     * @access  protected
     * @return  void
     * @throw   Hoa_Database_Constraint_Exception
     */
    protected function setCategoryType ( ) {

        if(null !== $this->getCategoryType())
            return;

        $type     = $this->getType();
        $category = null;

        if(preg_match('#^CHARACTER( VARYING)?#i', $type))
            $category = self::TYPE_STRING;

        elseif(preg_match('#^NATIONAL CHARACTER( VARYING)?#i', $type))
            $category = self::TYPE_NATIONAL_STRING;

        elseif(preg_match('#^(DECIMAL|NUMERIC)#i', $type))
            $category = self::TYPE_EXACT;

        elseif(preg_match('#^(FLOAT|REAL|DOUBLE PRECISION)#i', $type))
            $category = self::TYPE_APPROCHED;

        elseif(preg_match('#^(SMALLINT|INTEGER)#i', $type))
            $category = self::TYPE_INTEGER;

        elseif(preg_match('#^BIT( VARYING)?#i', $type))
            $category = self::TYPE_BINARY;

        elseif(preg_match('#^(TIMESTAMP|INTERVAL)#i', $type))
            $category = self::TYPE_TEMPORAL;

        elseif(preg_match('#^(DATE|TIME)#i', $type))
            $category = self::TYPE_DATETIME;

        elseif(preg_match('#^BOOL#i', $type))
            $category = self::TYPE_BOOLEAN;

        elseif(preg_match('#^(NATIONAL )?CHARACTER LARGE OBJECT#i', $type))
            $category = self::TYPE_CLOB;

        elseif(preg_match('#^BINARY LARGE OBJECT#i', $type))
            $category = self::TYPE_BLOB;

        elseif(preg_match('#(ARRAY|^ROW|^REF)#i', $type))
            $category = self::TYPE_STRUCTURE;

        else
            throw new Hoa_Database_Constraint_Exception(
                'Unknow %s type, must precise a category.', 3, $type);

        $this->setConstraint('category', $category);
    }

    /**
     * Check constraints.
     *
     * @access  protected
     * @return  void
     * @throw   Hoa_Database_Constraint_Exception
     */
    protected function checkConstraints ( ) {

        if($this->isPrimary() && $this->isNull())
            throw new Hoa_Database_Constraint_Exception(
                'A primary key cannot be null.', 4);

        if($this->isPrimary() && $this->isUnique())
            $this->setConstraint('unique', false);

        if($this->isPrimary() && $this->isIndex())
            $this->setConstraint('index', false);

        if($this->isUnique() && $this->isIndex())
            $this->setConstraint('index', false);

        if(null !== $this->getCharCollate() && null === $this->getCharSet())
            throw new Hoa_Database_Constraint_Exception(
                'If the charcollate is set, the charset must be set too.', 5);

        if(true === $this->isForeign() && false === strpos($this->getForeign(), '.'))
            throw new Hoa_Database_Constraint_Exception(
                'A foreign key must have this syntaxe : <table>.<field> ; given %s.',
                6, $this->getForeign());

        $this->checkType();
    }

    /**
     * Check the type.
     *
     * @access  protected
     * @return  void
     * @throw   Hoa_Database_Constraint_Exception
     * @todo    Manage the ARRAY type to know if it is an array of char. If
     *          yes, the charset and charcollate are allow.
     */
    protected function checkType ( ) {

        if(   null !== $this->getCharSet()
           && $this->getCategoryType() !== self::TYPE_STRING
           && $this->getCategoryType() !== self::TYPE_NATIONAL_STRING
           && $this->getCategoryType() !== self::TYPE_CLOB
           && $this->getCategoryType() !== self::TYPE_BLOB
           && $this->getCategoryType() !== self::TYPE_UDT)
            throw new Hoa_Database_Constraint_Exception(
                'A charset and a charcollate could not be set for this field.', 7);

        if(   true === $this->isAutoIncrement()
           && $this->getCategoryType() !== self::TYPE_EXACT
           && $this->getCategoryType() !== self::TYPE_APPROCHED
           && $this->getCategoryType() !== self::TYPE_INTEGER
           && $this->getCategoryType() !== self::TYPE_BINARY
           && $this->getCategoryType() !== self::TYPE_DATETIME
           && $this->getCategoryType() !== self::TYPE_UDT)
            throw new Hoa_Database_Constraint_Exception(
                'A field could be auto-incremented if and only if it is a ' .
                'number (real, approched, integer), a binary, a datetime, or ' .
                'maybe a UDT type.', 8);

        if(   true === $this->isAutoIncrement()
           && null !== $this->getDefaultValue())
            $this->setConstraint('default', null);

        if(    null !== $this->getDefaultValue()
           && ($this->getCategory() == self::TYPE_CLOB
           ||  $this->getCategory() == self::TYPE_BLOB))
            $this->setConstraint('default', null);
    }

    /**
     * Get the field type.
     *
     * @access  public
     * @return  string
     */
    public function getType ( ) {

        return $this->getConstraint('type');
    }

    /**
     * Get the field category type.
     *
     * @access  public
     * @return  string
     */
    public function getCategoryType ( ) {

        return $this->getConstraint('category');
    }

    /**
     * Check if the field value should be null or not.
     *
     * @access  public
     * @return  bool
     */
    public function isNull ( ) {

        return $this->getConstraint('isNull');
    }

    /**
     * Get the field default value.
     *
     * @access  public
     * @return  string
     */
    public function getDefaultValue ( ) {

        return $this->getConstraint('default');
    }

    /**
     * Check if the field is a primary key.
     *
     * @access  public
     * @return  bool
     */
    public function isPrimary ( ) {

        return $this->getConstraint('primary');
    }

    /**
     * Check if the field is a unique key.
     *
     * @access  public
     * @return  bool
     */
    public function isUnique ( ) {

        return $this->getConstraint('unique');
    }

    /**
     * Check if the field is a indexed key.
     *
     * @access  public
     * @return  bool
     */
    public function isIndex ( ) {

        return $this->getConstraint('index');
    }

    /**
     * Check if the field is a foreign key.
     *
     * @access  public
     * @return  bool
     */
    public function isForeign ( ) {

        return $this->getConstraint('foreign') !== null;
    }

    /**
     * Get the foreign key value.
     *
     * @access  public
     * @return  string
     */
    public function getForeign ( ) {

        return $this->getConstraint('foreign');
    }

    /**
     * Check if the field is auto-incremented.
     *
     * @access  public
     * @return  bool
     */
    public function isAutoIncrement ( ) {

        return $this->getConstraint('autoIncrement');
    }

    /**
     * Get the field comment.
     *
     * @access  public
     * @return  string
     */
    public function getComment ( ) {

        return $this->getConstraint('comment');
    }

    /**
     * Get the field check predicate.
     *
     * @access  public
     * @return  string
     */
    public function getCheck ( ) {

        return $this->getConstraint('check');
    }

    /**
     * Get the field char set.
     *
     * @access  public
     * @return  string
     */
    public function getCharSet ( ) {

        return $this->getConstraint('charSet');
    }

    /**
     * Get the field char collate.
     *
     * @access  public
     * @return  string
     */
    public function getCharCollate ( ) {

        return $this->getConstraint('charCollate');
    }
}

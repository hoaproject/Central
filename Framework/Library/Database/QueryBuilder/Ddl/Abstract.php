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
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Database_QueryBuilder_Ddl_Abstract
 *
 */

/**
 * Class Hoa_Database_QueryBuilder_Ddl_Abstract.
 *
 * Abstract class for DDL query builder.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_QueryBuilder_Ddl_Abstract
 */

abstract class Hoa_Database_QueryBuilder_Ddl_Abstract {

    /**
     * Subject.
     *
     * @var mixed object
     */
    protected $subject = null;



    /**
     * Constructor. Set the subject to observe.
     *
     * @access  public
     * @param   mixed   $subject    Field, table, base etc.
     * @return  void
     */
    public function __construct ( $subject ) {

        $this->set($subject);
    }

    /**
     * Set the subject to observe.
     *
     * @access  protected
     * @param   mixed      $subject    Field, table, base etc.
     * @return  mixed
     */
    protected function set ( $subject ) {

        $old           = $this->subject;
        $this->subject = $subject;

        return $old;
    }

    /**
     * Get the subject.
     *
     * @access  public
     * @return  mixed
     */
    public function get ( ) {

        return $this->subject;
    }

    /**
     * Check if the subject is a table.
     *
     * @access  public
     * @return  bool
     */
    public function isTable ( ) {

        return $this->get() instanceof Hoa_Database_Model_Table;
    }

    /**
     * Check if the subject is a field.
     *
     * @access  public
     * @return  bool
     */
    public function isField ( ) {

        return $this->get() instanceof Hoa_Database_Model_Field;
    }

    /**
     * Must implement the __toString method.
     *
     * @access  public
     * @return  string
     */
    abstract public function __toString ( );
}

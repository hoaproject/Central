<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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

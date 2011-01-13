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
 * @package     Hoa_Xyl
 * @subpackage  Hoa_Xyl_Interpreter_Html5_Textarea
 *
 */

/**
 * Hoa_Xyl_Interpreter_Html5_Generic
 */
import('Xyl.Interpreter.Html5.Generic') and load();

/**
 * Class Hoa_Xyl_Interpreter_Html5_Textarea.
 *
 * The <textarea /> component.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Xyl
 * @subpackage  Hoa_Xyl_Interpreter_Html5_Textarea
 */

class       Hoa_Xyl_Interpreter_Html5_Textarea
    extends Hoa_Xyl_Interpreter_Html5_Generic {

    /**
     * Map.
     *
     * @var Hoa_Xyl_Interpreter_Html5_Generic string
     */
    protected $_map      = 'textarea';

    /**
     * Whether the textarea is valid or not.
     *
     * @var Hoa_Xyl_Interpreter_Html5_Textarea bool
     */
    protected $_validity = true;



    /**
     * Set (or restore) the textarea value.
     *
     * @access  public
     * @param   string  $value    Value.
     * @return  string
     */
    public function setValue ( $value ) {

        $this->truncate(0);
        $this->writeAll($value);

        return;
    }

    /**
     * Get the textarea value.
     *
     * @access  public
     * @return  string
     */
    public function getValue ( ) {

        return $this->readAll();
    }

    /**
     * Unset the textarea value.
     *
     * @access  public
     * @return  void
     */
    public function unsetValue ( ) {

        $this->truncate(0);

        return;
    }

    /**
     * Check the textarea validity.
     *
     * @access  public
     * @param   mixed  $value    Value (if null, will find the value).
     * @return  bool
     */
    public function checkValidity ( $value = null ) {

        return $this->_validity;
    }

    /**
     * Whether the textarea is valid or not.
     *
     * @access  public
     * @return  bool
     */
    public function isValid ( ) {

        return $this->_validity;
    }
}

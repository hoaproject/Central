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
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Builder
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Tokenizer_Exception
 */
import('Tokenizer.Exception');

/**
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Hoa_Tokenizer_Style_Null
 */
import('Tokenizer.Style.Null');

/**
 * Class Hoa_Tokenizer_Builder.
 *
 * Build a tokened PHP source code.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Builder
 */

class Hoa_Tokenizer_Builder {

    /**
     * Tokenize result.
     *
     * @var Hoa_Tokenizer_Parser object
     */
    protected $_token  = null;

    /**
     * Collection of plugins.
     *
     * @var Hoa_Tokenizer_Builder array
     */
    protected $_plugin = array();

    /**
     * Style.
     *
     * @var Hoa_Tokenizer_Style_Interface object
     */
    protected $_style  = null;



    /**
     * Redirect to $this->setTokened().
     *
     * @access  public
     * @param   Hoa_Tokenizer_Parser  $tokened    Tokened PHP source code.
     * @return  void
     */
    public function __construct ( Hoa_Tokenizer_Parser $tokened = null ) {

        $this->setTokened($tokened);
        $this->setStyle(new Hoa_Tokenizer_Style_Null());
    }

    /**
     * Set tokened PHP source code.
     *
     * @access  protected
     * @param   Hoa_Tokenizer_Parser  $tokened    Tokened PHP source code.
     * @return  void
     */
    protected function setTokened ( Hoa_Tokenizer_Parser $tokened = null ) {

        if(null === $tokened)
            $tokened  = new Hoa_Tokenizer_Parser();

        $old          = $this->_token;
        $this->_token = $tokened;

        return $old;
    }

    /**
     * Get tokened PHP source code.
     *
     * @access  protected
     * @return  Hoa_Tokenizer_Parser
     */
    protected function getTokened ( ) {

        return $this->_token;
    }

    /**
     * Register a plugin.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Plugin_Interface  $plugin    Plugin.
     * @return  void
     */
    public function registerPlugin ( Hoa_Tokenizer_Plugin_Interface $plugin ) {

        $this->_plugin[] = $plugin;
    }

    /**
     * Define the current style.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Style_Interface  $style    Current style.
     * @return  Hoa_Tokenizer_Style_Interface
     */
    public function setStyle ( Hoa_Tokenizer_Style_Interface $style ) {

        $old          = $this->_style;
        $this->_style = $style;

        return $old;
    }

    /**
     * Get the current style.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Style_Interface
     */
    public function getStyle ( ) {

        return $this->_style;
    }

    /**
     * Get built code (alias of $this->__toString()).
     *
     * @access  public
     * @return  string
     */
    public function get ( ) {

        return $this->__toString();
    }

    /**
     * Build the code (transform to a string).
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        $out = null;

        foreach($this->getTokened()->get() as $i => $t) {

            list($token, $content, $lineNumber) = $t;

            $content  = $this->getStyle()->style($token, $content, $lineNumber);
            $out     .= $content;
        }

        return $out;
    }
}

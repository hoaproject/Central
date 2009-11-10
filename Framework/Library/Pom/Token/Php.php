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
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Php
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Pom_Token_Util_Exception
 */
import('Pom.Token.Util.Exception');

/**
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Hoa_Visitor_Element
 */
import('Visitor.Element');

/**
 * Class Hoa_Pom_Token_Php.
 *
 * Represent an open and a close tag.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Php
 */

class Hoa_Pom_Token_Php implements Hoa_Visitor_Element {

    /**
     * Tag.
     *
     * @var Hoa_Pom_Token_Php string
     */
    protected $_tag = null;

    /**
     * Start an open or a close tag.
     *
     * @access  public
     * @param   string  $tag    Tag.
     * @return  void
     */
    public function __construct ( $tag ) {

        $this->setTag($tag);
        $this->getType();

        return;
    }

    /**
     * Set an open or a close tag.
     *
     * @access  public
     * @param   string  $tag    Tag.
     * @return  string
     */
    public function setTag ( $tag ) {

        $old        = $this->_tag;
        $this->_tag = $tag;

        return $old;
    }

    /**
     * Get tag type.
     *
     * @access  public
     * @return  int
     * @throws  Hoa_Pom_Token_Util_Exception
     */
    public function getType ( ) {

        switch(strtolower($this->getTag())) {

            case '<?php':
            case '<?':
            case '<%':
                return Hoa_Pom::_OPEN_TAG;
              break;

            case '<?=':
            case '<%=':
                return Hoa_Pom::_OPEN_TAG_WITH_ECHO;
              break;

            case '?>':
            case '%>':
                return Hoa_Pom::_CLOSE_TAG;
              break;

            default:
                throw new Hoa_Pom_Token_Util_Exception(
                    'Tag %s is undefined.', 0, $this->getTag());
        }

        return -1;
    }

    /**
     * Get an open or a close tag.
     *
     * @access  public
     * @return  string
     */
    public function getTag ( ) {

        return $this->_tag;
    }

    /**
     * Accept a visitor.
     *
     * @access  public
     * @param   Hoa_Visitor_Visit  $visitor    Visitor.
     * @param   mixed              &$handle    Handle (reference).
     * @param   mixed              $eldnah     Handle (not reference).
     * @return  mixed
     */
    public function accept ( Hoa_Visitor_Visit $visitor,
                             &$handle = null,
                              $eldnah = null ) {

        return $visitor->visit($this, $handle, $eldnah);
    }
}

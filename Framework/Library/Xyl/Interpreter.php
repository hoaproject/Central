<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
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
 * @subpackage  Hoa_Xyl_Interpreter
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Xyl_Exception
 */
import('Xyl.Exception');

/**
 * Class Hoa_Xyl_Interpreter.
 *
 * 
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Xyl
 * @subpackage  Hoa_Xyl_Interpreter
 */

abstract class Hoa_Xyl_Interpreter {

    /**
     * Cache interpreter instance.
     *
     * @var Hoa_Xyl_Interpreter array
     */
    private static $_interpreterClassCache = array();

    /**
     * Current element (for a given interpreter).
     *
     * @var Hoa_Xyl_Element object
     */
    protected $_element                    = null;



    /**
     * Get interpreter class.
     *
     * @access  protected
     * @param   string  $elementName    Element name.
     * @return  Hoa_Xyl_Interpreter
     */
    abstract protected function getInterpreterClass ( $elementName );

    /**
     * Paint an element.
     *
     * @access  protected
     * @return  string
     */
    //abstract protected function paint ( );

    /**
     * Render an element.
     *
     * @access  public
     * @param   Hoa_Xyl_Element  $element    Element to render.
     * @return  string
     */
    public function render ( Hoa_Xyl_Element $element ) {

        $tagName = ucfirst(strtolower($element->getName()));

        if(!isset(self::$_interpreterClassCache[$tagName])) {

            $class = $this->getInterpreterClass($tagName);
            self::$_interpreterClassCache[$tagName] = $class;
        }
        else
            $class = self::$_interpreterClassCache[$tagName];

        $element->firstUpdate();
        $data     = &$element->getData();
        $out      = null;
        $renderer = new $class();
        $renderer->setElement($element);

        do {

            $out  .= $renderer->paint();
            $next  = is_array($data) ? next($data) : false;
            $element->update();

        } while(false !== $next);

        return $out;
    }

    /**
     * Set current element (for a given intepreter).
     *
     * @access  private
     * @param   Hoa_Xyl_Element  $element    Element.
     * @return  Hoa_Xyl_Element
     */
    private function setElement ( Hoa_Xyl_Element $element ) {

        $odl            = $this->_element;
        $this->_element = $element;

        return;
    }

    /**
     * Get current element.
     *
     * @access  protected
     * @return  Hoa_Xyl_Element
     */
    protected function getElement ( ) {

        return $this->_element;
    }
}

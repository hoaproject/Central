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
 * @package     Hoa_Tree
 * @subpackage  Hoa_Tree_Visitor_Dump
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Tree_Exception
 */
import('Tree.Exception');

/**
 * Hoa_Tree_Visitor_Abstract
 */
import('Tree.Visitor.Abstract');

/**
 * Hoa_Visitor_Visit
 */
import('Visitor.Visit');

/**
 * Class Hoa_Tree_Visitor_Dump.
 *
 * Dump a tree.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tree
 * @subpackage  Hoa_Tree_Visitor_Dump
 */

class          Hoa_Tree_Visitor_Dump
    extends    Hoa_Tree_Visitor_Abstract
    implements Hoa_Visitor_Visit {

    /**
     * Tree deep.
     *
     * @var Hoa_Tree_Visitor_Dump int
     */
    protected $_i = 0;



    /**
     * Visit an element.
     *
     * @access  public
     * @param   Hoa_Visitor_Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @return  string
     */
    public function visit ( Hoa_Visitor_Element $element, &$handle = null ) {

        $pre      = null;
        $in       = '> ' . str_repeat('  ', $this->_i) .
                    (string) $element->getValue() . "\n";
        $post     = null;
        $childs   = $element->getChilds();
        $i        = 0;
        $c        = count($childs);

        $this->_i++;

        for($max  = floor($c / 2); $i < $max; $i++)
            $pre  = $childs[$i]->accept($this, $handle);

        for(; $i < $c; $i++)
            $post = $childs[$i]->accept($this, $handle);

        $this->_i--;

        switch($this->getOrder()) {

            case parent::IN_ORDER:
                return $in  . $pre . $post;
              break;

            case parent::POST_ORDER:
                return $post . $in . $pre;
              break;

            default:
                return $pre  . $in . $post;
              break;
        }
    }
}

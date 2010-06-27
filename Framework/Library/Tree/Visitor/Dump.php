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
 * @package     Hoa_Tree
 * @subpackage  Hoa_Tree_Visitor_Dump
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

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
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
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
     * Just change the default transversal order value.
     *
     * @access  public
     * @param   int     $order    Traversal order (please, see the * self::*_ORDER
     *                            constants).
     * @return  void
     */
    public function __construct ( $order = parent::IN_ORDER ) {

        parent::__construct($order);

        return;
    }

    /**
     * Visit an element.
     *
     * @access  public
     * @param   Hoa_Visitor_Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visit ( Hoa_Visitor_Element $element,
                            &$handle = null,
                             $eldnah = null ) {

        $pre    = null;
        $in     = '> ' . str_repeat('  ', $this->_i) .
                  $element->getValue() . "\n";
        $post   = null;
        $childs = $element->getChilds();
        $i      = 0;
        $max    = floor(count($childs) / 2);

        $this->_i++;

        foreach($childs as $id => $child)
            if($i++ < $max)
                $pre  .= $child->accept($this, $handle, $eldnah);
            else
                $post .= $child->accept($this, $handle, $eldnah);

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

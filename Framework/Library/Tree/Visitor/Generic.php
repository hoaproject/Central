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
 */

namespace Hoa\Tree\Visitor {

/**
 * Class \Hoa\Tree\Visitor\Generic.
 *
 * Abstract tree visitor.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

abstract class Generic {

    /**
     * Pre-order traversal.
     *
     * @const int
     */
    const PRE_ORDER  = 0;

    /**
     * In-order traversal.
     *
     * @const int
     */
    const IN_ORDER   = 1;

    /**
     * Post-order traversal.
     *
     * @const int
     */
    const POST_ORDER = 2;

    /**
     * Traversal order.
     *
     * @var \Hoa\Tree\Visitor\Generic int
     */
    protected $_order = self::PRE_ORDER;



    /**
     * Build the visitor and set the traversal order.
     *
     * @access  public
     * @param   int     $order    Traversal order (please, see the self::*_ORDER
     *                            constants).
     * @return  void
     */
    public function __construct ( $order = self::PRE_ORDER ) {

        $this->setOrder($order);

        return;
    }

    /**
     * Set the traversal order.
     *
     * @access  protected
     * @param   int     $order    Traversal order (please, see the self::*_ORDER
     *                            constants).
     * @return  int
     */
    protected function setOrder ( $order ) {

        $old          = $this->_order;
        $this->_order = $order;

        return $old;
    }

    /**
     * Get the traversal order.
     *
     * @access  public
     * @return  int
     */
    public function getOrder ( ) {

        return $this->_order;
    }
}

}

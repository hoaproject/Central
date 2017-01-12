<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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
 */

namespace Hoa\Tree\Visitor;

/**
 * Class \Hoa\Tree\Visitor\Generic.
 *
 * Abstract tree visitor.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
abstract class Generic
{
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
     * @var int
     */
    protected $_order = self::PRE_ORDER;



    /**
     * Build the visitor and set the traversal order.
     *
     * @param   int     $order    Traversal order (please, see the self::*_ORDER
     *                            constants).
     */
    public function __construct($order = self::PRE_ORDER)
    {
        $this->setOrder($order);

        return;
    }

    /**
     * Set the traversal order.
     *
     * @param   int     $order    Traversal order (please, see the self::*_ORDER
     *                            constants).
     * @return  int
     */
    protected function setOrder($order)
    {
        $old          = $this->_order;
        $this->_order = $order;

        return $old;
    }

    /**
     * Get the traversal order.
     *
     * @return  int
     */
    public function getOrder()
    {
        return $this->_order;
    }
}

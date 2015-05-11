<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2015, Hoa community. All rights reserved.
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

namespace Hoa\Dispatcher;

use Hoa\Router;
use Hoa\View;

/**
 * Class \Hoa\Dispatcher\Kit.
 *
 * A structure, given to action, that holds some important data.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Kit
{
    /**
     * The router.
     *
     * @var \Hoa\Router
     */
    public $router     = null;

    /**
     * The dispatcher.
     *
     * @var \Hoa\Dispatcher
     */
    public $dispatcher = null;

    /**
     * The view.
     *
     * @var \Hoa\View\Viewable
     */
    public $view       = null;

    /**
     * Data from the view.
     *
     * @var \Hoa\Core\Data
     */
    public $data       = null;



    /**
     * Build a dispatcher kit.
     *
     * @param   \Hoa\Router           $router        The router.
     * @param   \Hoa\Dispatcher       $dispatcher    The dispatcher.
     * @param   \Hoa\View\Viewable    $view          The view.
     * @return  void
     */
    public function __construct(
        Router        $router,
        Dispatcher    $dispatcher,
        View\Viewable $view = null
    ) {
        $this->router     = $router;
        $this->dispatcher = $dispatcher;
        $this->view       = $view;

        if (null !== $view) {
            $this->data   = $view->getData();
        }

        return;
    }

    /**
     * This method is called just after the __construct() method.
     *
     * @return  void
     */
    public function construct()
    {
        return;
    }
}

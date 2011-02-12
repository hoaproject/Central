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

namespace Hoa\Controller {

/**
 * Class \Hoa\Controller\Application.
 *
 * A structure, given to action, that holds some important data.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Application {

    /**
     * The router.
     *
     * @var \Hoa\Controller\Router object
     */
    public $router     = null;

    /**
     * The dispatcher.
     *
     * @var \Hoa\Controller\Dispatcher object
     */
    public $dispatcher = null;

    /**
     * The view.
     *
     * @var \Hoa\View\Viewable object
     */
    public $view       = null;

    /**
     * Data from the view.
     *
     * @var \Hoa\Controller\Application mixed
     */
    public $data       = null;



    /**
     * Build an application controller.
     *
     * @access  public
     * @param   \Hoa\Controller\Router      $router        The router.
     * @param   \Hoa\Controller\Dispatcher  $dispatcher    The dispatcher.
     * @param   \Hoa\View\Viewable          $view          The view.
     * @return  void
     */
    final public function __construct ( Router             $router,
                                        Dispatcher         $dispatcher,
                                        \Hoa\View\Viewable $view = null ) {

        $this->router     = $router;
        $this->dispatcher = $dispatcher;
        $this->view       = $view;

        if(null !== $view)
            $this->data   = $view->getData();

        return;
    }

    /**
     * This method is called just after the __construct() method. You can
     * override it to initialize/construct your controller.
     *
     * @access  public
     * @return  void
     */
    public function construct ( ) {

        return;
    }
}

}

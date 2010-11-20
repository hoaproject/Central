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
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Application
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Class Hoa_Controller_Application.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Application
 */

class Hoa_Controller_Application {

    /**
     * The router.
     *
     * @var Hoa_Controller_Router object
     */
    public $router     = null;

    /**
     * The dispatcher.
     *
     * @var Hoa_Controller_Dispatcher object
     */
    public $dispatcher = null;

    /**
     * The view.
     *
     * @var Hoa_View_Viewable object
     */
    public $view       = null;

    /**
     * Data from the view.
     *
     * @var Hoa_Controller_Application mixed
     */
    public $data       = null;



    /**
     * Build an application controller.
     *
     * @access  public
     * @param   Hoa_Controller_Router      $router        The router.
     * @param   Hoa_Controller_Dispatcher  $dispatcher    The dispatcher.
     * @param   Hoa_View_Viewable          $view          The view.
     * @return  void
     */
    final public function __construct ( Hoa_Controller_Router     $router,
                                        Hoa_Controller_Dispatcher $dispatcher,
                                        Hoa_View_Viewable         $view = null ) {

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

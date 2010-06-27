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
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Plugin_Standard
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Controller_Plugin_Interface
 */
import('Controller.Plugin.Interface');

/**
 * Hoa_Controller_Exception_PluginIsAlreadyRegistered
 */
import('Controller.Exception.PluginIsAlreadyRegistered');

/**
 * Class Hoa_Controller_Plugin_Standard.
 *
 * Manage plugin; it means : register, unregister, and notify them.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Plugin_Standard
 */

class Hoa_Controller_Plugin_Standard {

    /**
     * Plugins list.
     *
     * @var Hoa_Controller_Plugin_Abstract array
     */
    private $_plugins = array();



    /**
     * Register plugin to notify.
     *
     * @access  public
     * @param   object  $plugin    Hoa_Controller_Plugin_Interface.
     * @return  void
     * @throw   Hoa_Controller_Exception_PluginIsAlreadyRegistered
     * @throw   Hoa_Controller_Exception
     */
    public function register ( Hoa_Controller_Plugin_Interface $plugin ) {

        $pluginIndex = get_class($plugin);

        if(isset($this->_plugins[$pluginIndex]))
            throw new Hoa_Controller_Exception_PluginIsAlreadyRegistered(
                'Plugin %s is already registered.', 0, $pluginIndex);

        $this->_plugins[$pluginIndex] = $plugin;

        return;
    }

    /**
     * Unregiser plugin.
     *
     * @access  public
     * @param   string  $pluginIndex    Plugin name.
     * @return  void
     */
    public function unregister ( $pluginIndex = '' ) {

        unset($this->_plugins[$pluginIndex]);

        return;
    }

    /**
     * Notify preRouter.
     *
     * @access  public
     * @param   Hoa_Core_Parameter  $parameters    Parameters.
     * @return  array
     * @throw   Hoa_Controller_Exception
     */
    public function notifyPreRouter ( Hoa_Core_Parameter $parameters ) {

        $return = array();

        foreach($this->_plugins as $index => $plugin)
            $return[$index] = $plugin->preRouter($request);

        return $return;
    }

    /**
     * Notify postRouter.
     *
     * @access  public
     * @param   Hoa_Core_Parameter         $parameters    Parameters.
     * @param   Hoa_Controller_Router_Standard  $router        Router.
     * @return  array
     * @throw   Hoa_Controller_Exception
     */
    public function notifyPostRouter ( Hoa_Core_Parameter        $parameters,
                                       Hoa_Controller_Router_Standard $router) {

        $return = array();

        foreach($this->_plugins as $index => $plugin)
            $return[$index] = $plugin->postRouter($request, $router);

        return $return;
    }

    /**
     * Notify preDispatcher.
     *
     * @access  public
     * @param   Hoa_Core_Parameter         $parameters    Parameters.
     * @param   Hoa_Controller_Router_Standard  $router        Router.
     * @return  array
     * @throw   Hoa_Controller_Exception
     */
    public function notifyPreDispatcher ( Hoa_Core_Parameter        $parameters,
                                          Hoa_Controller_Router_Standard $router) {

        $return = array();

        foreach($this->_plugins as $index => $plugin)
            $return[$index] = $plugin->preDispatcher($request, $router);

        return $return;
    }

    /**
     * Notify postDispatcher.
     *
     * @access  public
     * @param   Hoa_Core_Parameter             $parameters    Parameters.
     * @param   Hoa_Controller_Dispatcher_Abstract  $dispatcher    Dispatcher.
     * @param   string                              $dispatch      Dispatch
     *                                                             result.
     * @return  array
     * @throw   Hoa_Controller_Exception
     */
    public function notifyPostDispatcher ( Hoa_Core_Parameter            $parameters,
                                           Hoa_Controller_Dispatcher_Abstract $dispatcher,
                                           $dispatch) {

        $return = array();

        foreach($this->_plugins as $index => $plugin)
            $return[$index] = $plugin->postDispatcher($request, $dispatcher, $dispatch);

        return $return;
    }
}

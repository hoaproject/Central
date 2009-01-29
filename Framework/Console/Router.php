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
 * @package     Hoa_Console
 * @subpackage  Hoa_Console_Core_Router
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Console_Core_Exception
 */
import('Console.Core.Exception');

/**
 * Hoa_Console_Core_GetOption
 */
import('Console.Core.GetOption');

/**
 * Class Hoa_Console_Router.
 *
 * This class is a router, i.e. tries to found paths to files, directories etc.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Console
 * @subpackage  Hoa_Console_Router
 */

class Hoa_Console_Router {

    /**
     * The request instance.
     *
     * @var Hoa_Console_Request object
     */
    protected $_request = null;



    /**
     * Set the request.
     *
     * @access  public
     * @param   Hoa_Console_Request  $request    The request instance.
     * @return  Hoa_Console_Request
     */
    public function setRequest ( Hoa_Console_Request $request ) {

        $old            = $this->_request;
        $this->_request = $request;

        return $old;
    }

    /**
     * Get the request.
     *
     * @access  public
     * @return  Hoa_Console_Request
     */
    public function getRequest ( ) {

        return $this->_request;
    }

    /**
     * Route the command.
     *
     * @access  public
     * @param   string  $command    The command.
     * @return  void
     */
    public function route ( $input ) {

        $out = array();

        $group   = $this->getDefaultGroup();
        $command = $input;

        if(false !== strpos($input, $this->getSeparator()))
            list($group, $command) = explode($this->getSeparator(), $input);

        if(empty($group))
            $group = $this->getDefaultGroup();

        if(empty($command))
            $command = $this->getDefaultCommand();

        $this->setGroup($group);
        $this->setCommand($command);
        $this->setCommandFile($command);
        $this->setCommandClass($command);
    }

    /**
     * Verify if first letter is in upper case.
     *
     * @access  public
     * @param   string  $string    String.
     * @return  bool
     */
    public function isUcFirst ( $string = '' ) {

        return $string == ucfirst($string);
    }

    /**
     * Match a string.
     *
     * @access  public
     * @param   string  $pattern        Pattern.
     * @param   string  $replacement    Replacement string.
     * @return  string
     */
    public function transform ( $pattern = '', $replacement = '' ) {

        if(empty($pattern))
            return false;

        preg_match('#^([^\(]+)?(?:\(:([\w]+)\))?(.*)?$#', $pattern, $matches);

        list(, $pre, $var, $post) = $matches;

        if(!empty($var)) {

            if($this->isUcFirst($var))
                $replacement = ucfirst($replacement);

            $return = $pre . $replacement . $post;
        }
        else
            $return = $pattern;

        return $return;
    }

    /**
     * Set the group value.
     *
     * @access  protected
     * @param   string     $group    The group value.
     * @return  string
     */
    protected function setGroup ( $group ) {

        $old = $this->getRequest()->getParameter('system.group.value');

        $this->getRequest()->setParameter(
            'system.group.value',
            $this->transform(
                $this->getRequest()->getParameter('pattern.group'),
                $group
            )
        );

        return $old;
    }

    /**
     * Set the command value.
     *
     * @access  protected
     * @param   string     $command    The command value.
     * @return  string
     */
    protected function setCommand ( $command ) {

        $old = $this->getRequest()->getParameter('system.command.value');

        $this->getRequest()->setParameter(
            'system.command.value',
            $this->transform(
                $this->getRequest()->getParameter('pattern.command.name'),
                $command
            )
        );

        return $old;
    }

    /**
     * Set the command file.
     *
     * @access  protected
     * @param   string     $command    The command value.
     * @return  string
     */
    protected function setCommandFile ( $command ) {

        $old = $this->getRequest()->getParameter('system.command.file');

        $this->getRequest()->setParameter(
            'system.command.file',
            $this->transform(
                $this->getRequest()->getParameter('pattern.command.file'),
                $command
            )
        );

        return $old;
    }

    /**
     * Set the command class.
     *
     * @access  protected
     * @param   string     $command    The command value.
     * @return  string
     */
    protected function setCommandClass ( $command ) {

        $old = $this->getRequest()->getParameter('system.command.class');

        $this->getRequest()->setParameter(
            'system.command.class',
            $this->transform(
                $this->getRequest()->getParameter('pattern.command.class'),
                $command
            )
        );

        return $old;
    }

    /**
     * Get the default group.
     *
     * @access  public
     * @return  string
     */
    public function getDefaultGroup ( ) {

        return $this->getRequest()->getParameter('system.group.default');
    }

    /**
     * Get the default command.
     *
     * @access  public
     * @return  string
     */
    public function getDefaultCommand ( ) {

        return $this->getRequest()->getParameter('system.command.default');
    }

    /**
     * Get the group and command separator.
     *
     * @access  public
     * @return  string
     */
    public function getSeparator ( ) {

        return $this->getRequest()->getParameter('route.grpcmd.separator');
    }
}

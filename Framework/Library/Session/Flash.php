<?php

/**
 * Hoa
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

namespace {

from('Hoa')

/**
 * \Hoa\Session
 */
-> import('Session.~')

/**
 * \Hoa\Session\QNamespace
 */
-> import('Session.QNamespace')

/**
 * \Hoa\Session\Exception
 */
-> import('Session.Exception.~');

}

namespace Hoa\Session {

/**
 * Class \Hoa\Session\Flash.
 *
 * A flash is a temporary message transported in session.
 * Actually, it is a special namespace (reserved namespace).
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Flash extends QNamespace {

    /**
     * Built a new session flash message.
     *
     * @access  public
     * @param   string  $id         The flash message ID.
     * @param   string  $message    The flash message value.
     * @return  void
     * @throw   \Hoa\Session\Exception
     */
    public function __construct ( $id, $message = null ) {

        Session::setStrictMode(true);
        Session::start();

        $id = '_flashMessage_' . md5($id);

        parent::setNewNamespace($id);
        $this->setNewFlash($id, $message);

        return;
    }

    /**
     * Set a new flash message and prepare it.
     *
     * @access  protected
     * @param   string     $id         The flash message ID.
     * @param   string     $message    The flash message value.
     * @return  void
     * @throw   \Hoa\Session\Exception
     */
    protected function setNewFlash ( $id, $message = null ) {

        $_SESSION['__Hoa']['flash'][$id] = true;

        if(null === $this->getMessage())
            $_SESSION[$this->getNamespaceName()]['message'] = $message;

        return;
    }

    /**
     * Set a new message.
     *
     * @access  public
     * @param   string  $message    The flash message value.
     * @return  string
     */
    public function setMessage ( $message ) {

        parent::__set('message', $message);

        return;
    }

    /**
     * Get message.
     *
     * @access  public
     * @return  string
     */
    public function getMessage ( ) {

        return parent::__get('message');
    }

    /**
     * Disallow to overload property.
     *
     * @access  public
     * @param   string  $name     Variable name.
     * @param   mixed   $value    Variable value.
     * @return  string
     */
    public function __set ( $name, $value ) {

        return null;
    }

    /**
     * Disallow to overload property.
     *
     * @access  public
     * @param   string  $name    Variable name.
     * @return  string
     */
    public function __get ( $name ) {

        return null;
    }

    /**
     * Disallow to overload property.
     *
     * @access  public
     * @param   string  $name    Variable name.
     * @return  string
     */
    public function __isset ( $name ) {

        return null;
    }

    /**
     * Disallow to overload property.
     *
     * @access  public
     * @param   string  $name    Variable name.
     * @return  string
     */
    public function __unset ( $name ) {

        return null;
    }

    /**
     * Overload object output.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return $this->getMessage();
    }
}

}

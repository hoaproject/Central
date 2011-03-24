<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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

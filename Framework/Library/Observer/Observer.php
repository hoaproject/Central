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
 * \Hoa\Observer\Exception
 */
-> import('Observer.Exception');

}

namespace Hoa\Observer {

/**
 * Class \Hoa\Observer.
 *
 * Create an observer.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Observer {

    /**
     * Be silent.
     *
     * @const bool
     */
    const SILENT  = true;

    /**
     * Be verbose.
     *
     * @const bool
     */
    const VERBOSE = false;

    /**
     * Registered services.
     *
     * @var \Hoa\Observer array
     */
    protected static $_register = array();



    /**
     * Register a service.
     *
     * @access  public
     * @param   object  $service    Observable or observer service to register.
     * @param   string  $index      If $service is an observable, then it is its
     *                              ID, else if $service is an observer, it is
     *                              the observable service ID where it would be
     *                              registered.
     * @param   bool    $verbose    Verbosity mode, given with self::VERBOSE and
     *                              self::SILENT constants.
     * @return  void
     * @throw   \Hoa\Observer\Exception
     */
    public static function register ( $service, $index, $verbose = self::VERBOSE ) {

        if(   ($service instanceof \Hoa\Observer\IObserver\Observable)
           && ($service instanceof \Hoa\Observer\IObserver\Observer)) {

            if(false === self::isRegistered($index))
                self::$_register[$index]   = array();
            else
                self::$_register[$index][] = $service;

            return;
        }

        if($service instanceof \Hoa\Observer\IObserver\Observable) {

            if(true === self::isRegistered($index))
                if(self::VERBOSE === $verbose)
                    throw new Exception(
                        'Observable service %s is already registered.', 0, $index);
                else
                    return;

            self::$_register[$index] = array();

            return;
        }

        if($service instanceof \Hoa\Observer\IObserver\Observer) {

            if(false === self::isRegistered($index))
                if(self::VERBOSE === $verbose)
                    throw new Exception(
                        'Observer service %s cannot listen the observable ' .
                        'service %s because it is not registered.', 1,
                        array(get_class($service), $index));
                else
                    return;

            self::$_register[$index][] = $service;

            return;
        }

        throw new Exception(
            'Service %s must implement \Hoa\Observer\IObserver\Observer or ' .
            '\Hoa\Observer\IObserver\Observable interface.',
            2, get_class($service));

        return;
    }

    /**
     * Unregister a service.
     *
     * @access  public
     * @param   object  $service    Observable or observer service to register.
     * @param   mixed   $index      If $service is an observable, then it is its
     *                              ID, else if $service is an observer, it is
     *                              the $service ID where it is registered.
     *                              Given * will delete the observer service in
     *                              all observable services.
     * @return  void
     */
    public static function unregister ( $service, $index ) {

        if($service instanceof \Hoa\Observer\IObserver\Observable)
            if(true === self::isRegistered($index))
                unset(self::$_register[$index]);

        if($service instanceof \Hoa\Observer\IObserver\Observer) {

            $handle = get_class($service);

            if(true === self::isRegistered($index))
                foreach(self::$_register[$index] as $e => $observer) {

                    if($handle == get_class($observer))
                        unset(self::$_register[$index][$e]);
                }
            elseif($index == '*')
                foreach(self::$_register as $i => $observers)
                    foreach($observers as $e => $observer)
                        if($handle == get_class($observer))
                            unset(self::$_register[$i][$e]);
        }

        return;
    }

    /**
     * Check if service is already registered or not.
     *
     * @access  protected
     * @param   mixed      $index    Service name/index.
     * @return  bool
     */
    protected static function isRegistered ( $index ) {

        return isset(self::$_register[$index]);
    }

    /**
     * Notify an objet.
     *
     * @access  public
     * @param   string  $index        Service name/index.
     * @param   array   $arguments    Arguments to give to the
     *                                \Hoa\Observer\IObserver\Observable::update()
     *                                method.
     * @param   bool    $verbose      Verbosity mode, given with self::VERBOSE and
     *                                self::SILENT constants.
     * @return  void
     * @throw   \Hoa\Observer\Exception
     */
    public static function notify ( $index, Array $arguments = array(),
                                    $verbose = self::VERBOSE ) {

        if(false === self::isRegistered($index))
            if(self::VERBOSE === $verbose)
                throw new Exception(
                    'Cannot notify the observable service %s, ' .
                    'because it is not found.', 3, $index);
            else
                return;

        foreach(self::$_register[$index] as $i => $observer)
            call_user_func_array(
                array(
                    $observer,
                    'receiveNotification'
                ),
                array($index, $arguments)
            );

        return;
    }
}

}

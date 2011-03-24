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
 *
 *
 * @category    Framework
 * @package     Hoa_Filter
 * @subpackage  Hoa_Filter_Abstract
 *
 */

/**
 * Hoa_Filter_Exception
 */
import('Filter.Exception');

/**
 * Class Hoa_Filter_Abstract.
 *
 * The abstract class of all filters. Allow to manage the arguments of filters.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Filter
 * @subpackage  Hoa_Filter_Abstract
 */

abstract class Hoa_Filter_Abstract {

    /**
     * Needed arguments.
     *
     * @var Hoa_Filter_Abstract array
     */
    protected $arguments     = array();

    /**
     * The filter arguments.
     *
     * @var Hoa_Filter_Abstract array
     */
    private $filterArguments = array();



    /**
     * Set the needed arguments.
     *
     * @access  public
     * @param   array   $args    The arguments of the filter.
     * @return  void
     * @throw   Hoa_Filter_Exception
     */
    public function __construct ( Array $args = array() ) {

        $this->setFilterArguments($args);
    }

    /**
     * Check arguments of the filter.
     *
     * @access  protected
     * @return  bool
     * @throw   Hoa_Filter_Exception
     */
    protected function _checkArguments ( ) {

        $needed = array();
        $args   = $this->getFilterArguments();

        foreach($this->getArguments() as $name => $label)
            if(!isset($args[$name]))
                $needed[] = $name . ' : ' . $label;

        if(empty($needed))
            return true;

        $message = get_class($this) . ' needs parameters :' . "\n  - " .
                   implode("\n" . '  - ', $needed);

        throw new Hoa_Filter_Exception($message, 0);

        return false;
    }

    /**
     * Set arguments of the filter.
     *
     * @access  private
     * @param   array   $args    Arguments of the filter.
     * @return  array
     * @throw   Hoa_Filter_Exception
     */
    private function setFilterArguments ( Array $args = array() ) {

        $old                   = $this->filterArguments;
        $this->filterArguments = $args;

        $this->_checkArguments();

        return $old;
    }

    /**
     * Get an argument of the filter.
     *
     * @access  public
     * @param   string  $arg    The argument name.
     * @return  mixed
     * @throw   Hoa_Filter_Exception
     */
    public function getFilterArgument ( $name ) {

        if(   null !== $this->filterArguments[$name]
           && !isset($this->filterArguments[$name]))
            throw new Hoa_Filter_Exception(
                'The argument %s does not exit.', 1, $name);

        return $this->filterArguments[$name];
    }

    /**
     * Get arguments of the filter.
     *
     * @access  public
     * @return  array
     */
    public function getFilterArguments ( ) {

        return $this->filterArguments;
    }

    /**
     * Get needed arguments.
     *
     * @access  protected
     * @return  array
     */
    protected function getArguments ( ) {

        return $this->arguments;
    }

    /**
     * Force to implement filter method.
     *
     * @access  public
     * @param   string  $data    Data to filter.
     * @return  bool
     */
    abstract public function filter ( $string = null );
}

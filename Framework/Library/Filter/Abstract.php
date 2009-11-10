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
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Filter
 * @subpackage  Hoa_Filter_Abstract
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

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
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
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

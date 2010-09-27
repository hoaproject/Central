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
 * @package     Hoa_Xyl
 * @subpackage  Hoa_Xyl_Element_Concrete
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Xyl_Exception
 */
import('Xyl.Exception');

/**
 * Hoa_Xml_Element_Concrete
 */
import('Xml.Element.Concrete') and load();

/**
 * Hoa_Xyl_Element
 */
import('Xyl.Element') and load();

/**
 * Class Hoa_Xyl_Element_Concrete.
 *
 * 
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Xyl
 * @subpackage  Hoa_Xyl_Element_Concrete
 */

abstract class Hoa_Xyl_Element_Concrete
    extends    Hoa_Xml_Element_Concrete
    implements Hoa_Xyl_Element {

    /**
     * Data bucket.
     *
     * @var Hoa_Xyl_Element_Concrete array
     */
    private $_bucket = array('data' => null);



    /**
     * Distribute data into the XYL tree. Data are linked to element through a
     * reference to the data bucket in the super root.
     *
     * @access  public
     * @return  void
     */
    public function computeDataBinding ( Array &$data, Array &$parent = null ) {

        $e = $this->getAbstractElement();

        if(false === $e->attributeExists('bind')) {

            foreach($this as $element)
                $element->computeDataBinding($data, $parent);

            $this->execute();

            return;
        }

        $source = $e->readAttribute('bind');
        $handle = $data;

        if('?' == $source) {

            foreach($this as $element)
                $element->computeDataBinding($handle, $parent);

            $this->execute();

            return;
        }
        elseif('?' == $source[0]) {

            $source  = trim(substr($source, 1), '/');
            $explode = explode('/', $source);
            $branche = $explode[count($explode) - 1];
            array_pop($explode);

            foreach($explode as $i => $part)
                if(isset($handle[0][$part]))
                    $handle = &$handle[0][$part];
                elseif(isset($handle[$part]))
                    $handle = &$handle[$part];
        }
        else
            throw new Hoa_Xyl_Exception(
                'Huh?', 0);

        $this->_bucket['parent']  = &$parent;
        $this->_bucket['current'] = 0;
        $this->_bucket['branche'] = $branche;

        if(isset($handle[$branche])) {

            if(null === $parent)
                $this->_bucket['data'] = array(0 => &$handle);

            if(is_array($handle[$branche]))
                foreach($this as $element)
                    $element->computeDataBinding($handle[$branche], $this->_bucket);
        }
        elseif(isset($handle[0][$branche])) {

            if(null === $parent)
                $this->_bucket['data'] = &$handle;

            if(is_array($handle[0][$branche]))
                foreach($this as $element)
                    $element->computeDataBinding($handle[0][$branche], $this->_bucket);
        }

        $this->execute();

        return;
    }

    /**
     * Get data of this element.
     *
     * @access  public
     * @return  array
     */
    public function &getData ( ) {

        return $this->_bucket['data'];
    }

    /**
     * Get current data of this element.
     *
     * @access  public
     * @return  mixed
     */
    public function getCurrentData ( ) {

        $current = current($this->_bucket['data']);

        return $current[$this->_bucket['branche']];
    }

    /**
     * First update for iterate data bucket.
     *
     * @access  private
     * @return  void
     */
    private function firstUpdate ( ) {

        if(!isset($this->_bucket['parent']))
            return;

        $parent                  = &$this->_bucket['parent'];
        $this->_bucket['data']    = &$parent['data'][$parent['current']][$parent['branche']];
        $this->_bucket['current'] = 0;

        if(!isset($this->_bucket['data'][0])) {

            unset($this->_bucket['data']);
            $this->_bucket['data'] = array(
                &$parent['data'][$parent['current']][$parent['branche']]
            );
        }

        return $this->_bucket;
    }

    /**
     * Continue to update the data bucket while iterating.
     *
     * @access  private
     * @return  void
     */
    private function update ( ) {

        if(!is_array($this->_bucket['data']))
            return;

        $this->_bucket['current'] = key($this->_bucket['data']);

        return $this->_bucket;
    }

    /**
     * Make the render of the XYL tree.
     *
     * @access  public
     * @return  string
     */
    public function render ( ) {

        $this->firstUpdate();
        $data = &$this->getData();
        $out  = null;

        do {

            $out  .= $this->paint();
            $next  = is_array($data) ? next($data) : false;
            $this->update();

        } while(false !== $next);

        return $out;
    }

    /**
     * Paint the element.
     *
     * @access  protected
     * @return  string
     */
    abstract protected function paint ( );

    /**
     * Execute an element.
     *
     * @access  protected
     * @return  void
     */
    protected function execute ( ) {

        return;
    }
}

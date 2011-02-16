<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
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
 * \Hoa\Xyl\Exception
 */
-> import('Xyl.Exception')

/**
 * \Hoa\Xml\Element\Concrete
 */
-> import('Xml.Element.Concrete')

/**
 * \Hoa\Xyl\Element
 */
-> import('Xyl.Element.~');

}

namespace Hoa\Xyl\Element {

/**
 * Class \Hoa\Xyl\Element\Concrete.
 *
 * 
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

abstract class Concrete extends \Hoa\Xml\Element\Concrete implements Element {

    /**
     * Data bucket.
     *
     * @var \Hoa\Xyl\Element\Concrete array
     */
    private $_bucket           = array('data' => null);

    /**
     * Visibility.
     *
     * @var \Hoa\Xyl\Element\Concrete bool
     */
    protected $_visibility     = true;

    /**
     * Transient value.
     *
     * @var \Hoa\Xyl\Element\Concrete string
     */
    protected $_transientValue = null;



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

            if($this instanceof \Hoa\Xyl\Element\Executable)
                $this->execute();

            return;
        }

        $source = $e->readAttribute('bind');
        $handle = $data;

        if('?' == $source) {

            foreach($this as $element)
                $element->computeDataBinding($handle, $parent);

            if($this instanceof \Hoa\Xyl\Element\Executable)
                $this->execute();

            return;
        }
        elseif('?' == $source[0]) {

            $source  = trim(substr($source, 1), '/');
            $explode = explode('/', $source);
            $branche = $explode[count($explode) - 1];
            array_pop($explode);

            foreach($explode as $i => $part)
                $handle = &$handle[0][$part];
        }
        else
            throw new \Hoa\Xyl\Exception(
                'Huh?', 0);

        $this->_bucket['parent']  = &$parent;
        $this->_bucket['current'] = 0;
        $this->_bucket['branche'] = $branche;

        if(null === $parent)
            $this->_bucket['data'] = &$handle;

        foreach($this as $element)
            $element->computeDataBinding($handle[0][$branche], $this->_bucket);

        if($this instanceof \Hoa\Xyl\Element\Executable)
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

        return $this->_bucket['data']
                             [$this->_bucket['current']]
                             [$this->_bucket['branche']];
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

        $parent                   = &$this->_bucket['parent'];
        $this->_bucket['data']    = &$parent['data'][$parent['current']]
                                            [$parent['branche']];
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
     * @return  boolean
     */
    private function update ( ) {

        if(!is_array($this->_bucket['data']))
            return;

        $this->_bucket['current'] = key($this->_bucket['data']);
        $handle                   = current($this->_bucket['data']);

        return isset($handle[$this->_bucket['branche']]);
    }

    /**
     * Make the render of the XYL tree.
     *
     * @access  public
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    public function render ( \Hoa\Stream\IStream\Out $out ) {

        if(false === $this->getVisibility())
            return;

        $this->firstUpdate();
        $data = &$this->getData();

        do {

            $this->paint($out);
            $next  = is_array($data) ? next($data) : false;
            $next  = $next && $this->update();

        } while(false !== $next);

        return;
    }

    /**
     * Paint the element.
     *
     * @access  protected
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    abstract protected function paint ( \Hoa\Stream\IStream\Out $out );

    /**
     * Compute value. If the @bind attribute existss, compute the current data,
     * else compute the abstract element casted as string if no child is
     * present, else rendering all children.
     *
     * @access  public
     * @return  string
     */
    public function computeValue ( \Hoa\Stream\IStream\Out $out = null ) {

        $data = false;

        if($this->getAbstractElement()->attributeExists('bind'))
            $data = $this->_transientValue
                  = $this->getCurrentData();

        elseif(0 == count($this))
            $data = $this->_transientValue
                  = $this->getAbstractElement()->readAll();

        if(null === $out)
            return $data;

        if(false !== $data) {

            $out->writeAll($data);

            return;
        }

        foreach($this as $child)
            $child->render($out);

        return;
    }

    /**
     * Get transient value, i.e. get the last compute value if exists (if no
     * exists, compute right now).
     *
     * @access  public
     * @return  string
     */
    public function computeTransientValue ( \Hoa\Stream\IStream\Out $out = null ) {

        $data = $this->_transientValue;

        if(null === $data)
            return $this->computeValue($out);

        if(null === $out)
            return $data;

        $out->writeAll($data);

        return;
    }

    /**
     * Clean transient value.
     *
     * @access  public
     * @return  void
     */
    public function cleanTransientValue ( ) {

        $this->_transientValue = null;

        return;
    }

    /**
     * Read attributes as a string.
     *
     * @access  public
     * @return  string
     */
    public function readAttributesAsString ( ) {

        $out        = null;
        $attributes = $this->getAbstractElement()->readAttributes();
        unset($attributes['bind']);

        foreach($attributes as $name => $value)
            $out .= ' ' . $name . '="' . str_replace('"', '\"', $value) . '"';

        return $out;
    }

    /**
     * Set visibility.
     *
     * @access  public
     * @param   bool    $visibility    Visibility.
     * @return  bool
     */
    public function setVisibility ( $visibility ) {

        $old               = $this->_visibility;
        $this->_visibility = $visibility;

        return $old;
    }

    /**
     * Get visibility.
     *
     * @access  public
     * @return  bool
     */
    public function getVisibility ( ) {

        return $this->_visibility;
    }
}

}

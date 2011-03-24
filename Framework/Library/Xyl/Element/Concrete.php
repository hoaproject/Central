<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
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

            if($this instanceof \Hoa\Xyl\Element\Executable)
                $this->preExecute();

            foreach($this as $element)
                $element->computeDataBinding($data, $parent);

            if($this instanceof \Hoa\Xyl\Element\Executable)
                $this->postExecute();

            return;
        }

        $source = $e->readAttribute('bind');
        $handle = $data;

        if('?' == $source) {

            if($this instanceof \Hoa\Xyl\Element\Executable)
                $this->preExecute();

            foreach($this as $element)
                $element->computeDataBinding($handle, $parent);

            if($this instanceof \Hoa\Xyl\Element\Executable)
                $this->postExecute();

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

        if($this instanceof \Hoa\Xyl\Element\Executable)
            $this->preExecute();

        if(isset($handle[0][$branche]))
            foreach($this as $element)
                $element->computeDataBinding($handle[0][$branche], $this->_bucket);

        if($this instanceof \Hoa\Xyl\Element\Executable)
            $this->postExecute();

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

        if(!isset($this->_bucket['data']))
            return;

        $current = $this->_bucket['data'][$this->_bucket['current']];

        if(!isset($current[$this->_bucket['branche']]))
            return null;

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

        $data  = false;
        $count = 0 == count($this);

        if($this->getAbstractElement()->attributeExists('bind'))
            $data = $this->_transientValue
                  = $this->getCurrentData();

        elseif(true === $count)
            $data = $this->_transientValue
                  = $this->getAbstractElement()->readAll();

        if(null === $out)
            return $data;

        if(false !== $data && true === $count) {

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

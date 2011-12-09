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
 * This class represents the top-XYL-element. It manages data binding, value
 * computing etc.
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
     * Attribute data bucket.
     *
     * @var \Hoa\Xyl\Element\Concrete array
     */
    private $_attributeBucket  = null;

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
     * Attributes description.
     *
     * @var \Hoa\Xyl\Element\Concrete array
     */
    protected $_attributes     = null;



    /**
     * Distribute data into the XYL tree. Data are linked to element through a
     * reference to the data bucket in the super root.
     *
     * @access  public
     * @param   array   &$data      Data.
     * @param   array   &$parent    Parent.
     * @return  void
     */
    public function computeDataBinding ( Array &$data, Array &$parent = null ) {

        $bucket     = $data;
        $executable = $this instanceof Executable;
        $bindable   = false;

        if(   true === $this->abstract->attributeExists('href')
           && 0 !== preg_match('#\(\?[^\)]+\)#', $this->abstract->readAttribute('href')))
            $bindable = true;

        if(   false === $this->abstract->attributeExists('bind')
           || null  === $bind = $this->selectData(
                                    $this->abstract->readAttribute('bind'),
                                    $bucket
                                )) {

            $bindable   and $this->_attributeBucket = &$parent;
            $executable and $this->preExecute();

            foreach($this as $element)
                $element->computeDataBinding($data, $parent);

            $executable and $this->postExecute();

            return;
        }

        $this->_bucket['parent']  = &$parent;
        $this->_bucket['current'] = 0;
        $this->_bucket['branche'] = $bind;

        if(null === $parent)
            $this->_bucket['data'] = $bucket;

        $bindable   and $this->_attributeBucket = &$this->_bucket;
        $executable and $this->preExecute();

        if(isset($bucket[0][$bind]))
            foreach($this as $element)
                $element->computeDataBinding($bucket[0][$bind], $this->_bucket);

        $executable and $this->postExecute();
        unset($bucket);

        return;
    }

    /**
     * Select data according to an expression into a bucket.
     * Move pointer into bucket or fill a new bucket and return the last
     * reachable branche.
     *
     * @access  protected
     * @param   string     $expression    Expression (please, see inline
     *                                    comments to study all cases).
     * @param   array      &$bucket       Bucket.
     * @return  string
     */
    protected function selectData ( $expression, Array &$bucket ) {

        if('?' != $expression[0] || '?' == $expression)
            return null;

        // ?a/b/c
        // ?p:a/b/c
        // ?path:a/b/c
        if(0 !== preg_match('#^\?(?:p(?:ath)?:)?([^:]+)$#i', $expression, $matches)) {

            $split = preg_split(
                '#(?<!\\\)\/#',
                $matches[1]
            );

            foreach($split as &$s)
                $s = str_replace('\/', '/', $s);

            $branche = array_pop($split);

            foreach($split as $part)
                $bucket = &$bucket[0][$part];

            return $branche;
        }

        // ?q:a b c
        // ?query:a b c
        elseif(0 !== preg_match('#^\?q(?:uery)?:(.*)?$#i', $expression, $matches)) {

            dump('*** QUERY');
            dump($matches);
        }

        // ?x:a/b/c
        // ?xpath:a/b/c
        elseif(0 !== preg_match('#^\?x(?:path)?:(.*)?$#i', $expression, $matches)) {

            dump('*** XPATH');
            dump($matches);
        }

        return null;
    }

    /**
     * Get current data of this element.
     *
     * @access  protected
     * @return  mixed
     */
    protected function getCurrentData ( ) {

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
     * @return  bool
     */
    private function update ( ) {

        if(!is_array($this->_bucket['data']))
            return false;

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
        $data = &$this->_bucket['data'];

        do {

            $this->paint($out);
            $next = is_array($data) ? next($data) : false;
            $next = $next && $this->update();

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
     * @param   \Hoa\Stream\IStream\Out  $out    Output stream. If null, we
     *                                           return the result.
     * @return  string
     */
    public function computeValue ( \Hoa\Stream\IStream\Out $out = null ) {

        $data  = false;
        $count = 0 == count($this);

        if($this->abstract->attributeExists('bind'))
            $data = $this->_transientValue
                  = $this->getCurrentData();

        elseif(true === $count)
            $data = $this->_transientValue
                  = $this->abstract->readAll();

        if(null === $out)
            return $this->readAll();

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
     * @param   \Hoa\Stream\IStream\Out  $out    Output stream. If null, we
     *                                           return the result.
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
     * Compute attribute value.
     *
     * @access  public
     * @param   string  $attribute    Attribute name.
     * @param   array   $variables    Variables.
     * @return  string
     */
    public function computeAttributeValue ( $attribute,
                                            Array $variables = array() ) {

        if(false === $this->abstract->attributeExists($attribute))
            return null;

        $value = $this->abstract->readAttribute($attribute);

        // (!variable).
        $value = preg_replace_callback(
            '#\(\!([^\)]+)\)#',
            function ( Array $matches ) use ( &$variables ) {

                if(!isset($variables[$matches[1]]))
                    return '';

                return $variables[$matches[1]];
            },
            $value
        );

        // (?inner-bind).
        $handle = &$this->_attributeBucket;
        $data   = $handle['data'][$handle['current']][$handle['branche']];

        if(is_array($data) && isset($data[0]))
            $data = $data[0];

        $value  = preg_replace_callback(
            '#\(\?([^\)]+)\)#',
            function ( Array $matches ) use ( &$data ) {

                if(!is_array($data) || !isset($data[$matches[1]]))
                    return '';

                return $data[$matches[1]];
            },
            $value
        );

        return $value;
    }

    /**
     * Clean transient value.
     *
     * @access  protected
     * @return  void
     */
    protected function cleanTransientValue ( ) {

        $this->_transientValue = null;

        return;
    }

    /**
     * Compute link.
     *
     * @access  public
     * @param   string  $link    Link.
     * @return  string
     */
    public function computeLink ( $link ) {

        $router = $this->getAbstractElementSuperRoot()->getRouter();

        if(null === $router)
            return $link;

        if(0 != preg_match('#^@(?:(?:([^:]+):(.*))|([^$]+))$#', $link, $matches)) {

            if(isset($matches[3]))
                return $router->unroute($matches[3]);

            $id = $matches[1];
            $kv = array();

            foreach(explode('&', $matches[2]) as $value) {

                $handle                    = explode('=', $value);
                $kv[urldecode($handle[0])] = urldecode($handle[1]);
            }

            return $router->unroute($id, $kv);
        }

        return $link;
    }

    /**
     * Read attributes as a string.
     *
     * @access  public
     * @return  string
     */
    public function readAttributesAsString ( ) {

        $out        = null;
        $attributes = $this->abstract->readAttributes();
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

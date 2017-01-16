<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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

namespace Hoa\Xyl\Element;

use Hoa\Stream;
use Hoa\Stringbuffer;
use Hoa\Xml;
use Hoa\Xyl;

/**
 * Class \Hoa\Xyl\Element\Concrete.
 *
 * This class represents the top-XYL-element. It manages data binding, value
 * computing etc.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
abstract class Concrete extends Xml\Element\Concrete implements Element
{
    /**
     * Attribute type: unknown.
     *
     * @const int
     */
    const ATTRIBUTE_TYPE_UNKNOWN =  0;

    /**
     * Attribute type: normal.
     *
     * @const int
     */
    const ATTRIBUTE_TYPE_NORMAL  =  1;

    /**
     * Attribute type: id (if it represents an ID).
     *
     * @const int
     */
    const ATTRIBUTE_TYPE_ID      =  2;

    /**
     * Attribute type: custom (e.g. data-*).
     *
     * @const int
     */
    const ATTRIBUTE_TYPE_CUSTOM  =  4;

    /**
     * Attribute type: list (e.g. the class attribute).
     *
     * @const int
     */
    const ATTRIBUTE_TYPE_LIST    =  8;

    /**
     * Attribute type: link (if it is a link).
     *
     * @const int
     */
    const ATTRIBUTE_TYPE_LINK    = 16;

    /**
     * Data bucket.
     *
     * @var array
     */
    private $_bucket              = ['data' => null];

    /**
     * Data bucket for attributes.
     *
     * @var array
     */
    private $_attributeBucket     = null;

    /**
     * Visibility.
     *
     * @var bool
     */
    protected $_visibility        = true;

    /**
     * Transient value.
     *
     * @var string
     */
    protected $_transientValue    = null;

    /**
     * Attributes description.
     * Each element can declare its own attributes and inherit its parent's
     * attributes.
     *
     * @var array
     */
    protected static $_attributes = [
        'id'        => self::ATTRIBUTE_TYPE_ID,
        'title'     => self::ATTRIBUTE_TYPE_NORMAL,
        'lang'      => self::ATTRIBUTE_TYPE_NORMAL,
        'translate' => self::ATTRIBUTE_TYPE_NORMAL,
        'dir'       => self::ATTRIBUTE_TYPE_NORMAL,
        'class'     => self::ATTRIBUTE_TYPE_LIST,
        'style'     => self::ATTRIBUTE_TYPE_NORMAL,
        'data'      => self::ATTRIBUTE_TYPE_CUSTOM,
        'aria'      => self::ATTRIBUTE_TYPE_CUSTOM,
        'role'      => self::ATTRIBUTE_TYPE_NORMAL
    ];

    /**
     * Whether this is the last iteration or not.
     *
     * @var bool
     */
    protected $_lastIteration     = false;



    /**
     * Distribute data into the XYL tree. Data are linked to element through a
     * reference to the data bucket in the super root.
     *
     * @param   array   &$data      Data.
     * @param   array   &$parent    Parent.
     * @return  void
     */
    public function computeDataBinding(array &$data, array &$parent = null)
    {
        $executable = $this instanceof Executable;
        $bindable   = $this->abstract->attributeExists('bind');

        if (false === $bindable) {
            foreach (static::getDeclaredAttributes() as $attribute => $type) {
                $bindable |=
                    0 !== preg_match(
                        '#\(\?[^\)]+\)#',
                        $this->abstract->readAttribute($attribute)
                    );
            }

            $bindable = (bool) $bindable;
        }

        // Propagate binding.
        if (false === $bindable) {
            $executable and $this->preExecute();

            foreach ($this as $element) {
                $element->computeDataBinding($data, $parent);
            }

            $executable and $this->postExecute();

            return;
        }

        // Inner-binding.
        if (false === $this->abstract->attributeExists('bind')) {
            if (null === $parent) {
                $parent = [
                    'parent'  => null,
                    'current' => 0,
                    'branche' => '_',
                    'data'    => [[
                        '_' => $data
                    ]]
                ];
            }

            $this->_attributeBucket = &$parent;
            $executable and $this->preExecute();

            foreach ($this as $element) {
                $element->computeDataBinding($data, $parent);
            }

            $executable and $this->postExecute();

            return;
        }

        // Binding.
        $this->_bucket['parent']  = &$parent;
        $this->_bucket['current'] = 0;
        $this->_bucket['branche'] = $bind = $this->selectData(
            $this->abstract->readAttribute('bind'),
            $data
        );

        if (null === $parent) {
            $this->_bucket['data'] = $data;
        }

        $bindable   and $this->_attributeBucket = &$parent;
        $executable and $this->preExecute();

        if (isset($data[0][$bind])) {
            if (is_string($data[0][$bind])) {
                return;
            }

            foreach ($this as $element) {
                $element->computeDataBinding($data[0][$bind], $this->_bucket);
            }
        }

        $executable and $this->postExecute();

        return;
    }

    /**
     * Select data according to an expression into a bucket.
     * Move pointer into bucket or fill a new bucket and return the last
     * reachable branche.
     *
     * @param   string     $expression    Expression (please, see inline
     *                                    comments to study all cases).
     * @param   array      &$bucket       Bucket.
     * @return  string
     */
    protected function selectData($expression, array &$bucket)
    {
        switch (Xyl::getSelector($expression, $matches)) {
            case Xyl::SELECTOR_PATH:
                $split = preg_split(
                    '#(?<!\\\)\/#',
                    $matches[1]
                );

                foreach ($split as &$s) {
                    $s = str_replace('\/', '/', $s);
                }

                $branche = array_pop($split);
                $handle  = &$bucket;

                foreach ($split as $part) {
                    $handle = &$bucket[0][$part];
                }

                $bucket = $handle;

                return $branche;

            case Xyl::SELECTOR_QUERY:
                var_dump('*** QUERY');
                var_dump($matches);

                break;

            case Xyl::SELECTOR_XPATH:
                var_dump('*** XPATH');
                var_dump($matches);

                break;
        }

        return null;
    }

    /**
     * Get current data of this element.
     *
     * @return  mixed
     */
    protected function getCurrentData()
    {
        if (empty($this->_bucket['data'])) {
            return;
        }

        $current = $this->_bucket['data'][$this->_bucket['current']];

        if (!isset($current[$this->_bucket['branche']])) {
            return null;
        }

        return $current[$this->_bucket['branche']];
    }

    /**
     * First update for iterate data bucket.
     *
     * @return  void
     */
    private function firstUpdate()
    {
        if (!isset($this->_bucket['parent'])) {
            return;
        }

        $parent                = &$this->_bucket['parent'];
        $this->_bucket['data'] = &$parent['data'][$parent['current']][$parent['branche']];
        reset($this->_bucket['data']);
        $this->_bucket['current'] = 0;

        if (!isset($this->_bucket['data'][0])) {
            unset($this->_bucket['data']);
            $this->_bucket['data'] = [
                &$parent['data'][$parent['current']][$parent['branche']]
            ];
        }

        return;
    }

    /**
     * Continue to update the data bucket while iterating.
     *
     * @return  bool
     */
    private function update()
    {
        if (!is_array($this->_bucket['data'])) {
            return false;
        }

        $this->_bucket['current'] = key($this->_bucket['data']);
        $handle                   = current($this->_bucket['data']);

        return isset($handle[$this->_bucket['branche']]);
    }

    /**
     * Make the render of the XYL tree.
     *
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    public function render(Stream\IStream\Out $out)
    {
        if (false === $this->getVisibility()) {
            return;
        }

        $this->firstUpdate();

        if (isset($this->_bucket['branche']) &&
            (empty($this->_bucket['data']) ||
            empty($this->_bucket['data'][$this->_bucket['current']][$this->_bucket['branche']]))) {
            return;
        }

        $data = &$this->_bucket['data'];

        do {
            $next                 = is_array($data) ? next($data) : false;
            $this->_lastIteration = false === $next;

            $this->paint($out);
            $next = $next && $this->update();
        } while (false !== $next);

        return;
    }

    /**
     * Paint the element.
     *
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    abstract protected function paint(Stream\IStream\Out $out);

    /**
     * Compute value. If the @bind attribute exists, compute the current data,
     * else compute the abstract element casted as string if no child is
     * present, else rendering all children.
     *
     * @param   \Hoa\Stream\IStream\Out  $out    Output stream. If null, we
     *                                           return the result.
     * @return  string
     */
    public function computeValue(Stream\IStream\Out $out = null)
    {
        $data = false;

        if (true === $this->abstract->attributeExists('bind')) {
            $data = $this->_transientValue = $this->getCurrentData();
        }

        if (null === $out) {
            if (false !== $data) {
                return $data;
            } else {
                return $data = $this->_transientValue = $this->abstract->readAll();
            }
        }

        if (0 === count($this)) {
            if (false !== $data) {
                $out->writeAll($data);
            } else {
                $out->writeAll($this->abstract->readAll());
            }

            return;
        }

        foreach ($this as $child) {
            $child->render($out);
        }

        return;
    }

    /**
     * Get transient value, i.e. get the last compute value if exists (if no
     * exists, compute right now).
     *
     * @param   \Hoa\Stream\IStream\Out  $out    Output stream. If null, we
     *                                           return the result.
     * @return  string
     */
    public function computeTransientValue(Stream\IStream\Out $out = null)
    {
        $data = $this->_transientValue;

        if (null === $data) {
            return $this->computeValue($out);
        }

        if (null === $out) {
            return $data;
        }

        $out->writeAll($data);

        return;
    }

    /**
     * Clean transient value.
     *
     * @return  void
     */
    protected function cleanTransientValue()
    {
        $this->_transientValue = null;

        return;
    }

    /**
     * Compute attribute value.
     *
     * @param   string  $value    Attribute value.
     * @param   int     $type     Attribute type.
     * @param   string  $name     Attribute name.
     * @return  string
     */
    public function computeAttributeValue(
        $value,
        $type = self::ATTRIBUTE_TYPE_UNKNOWN,
        $name = null
    ) {
        /*
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
        */

        // (?inner-bind).
        if (null !== $this->_attributeBucket ||
            !empty($this->_bucket['data'])) {
            if (null === $this->_attributeBucket) {
                $handle = &$this->_bucket;
                $data   = $handle['data'][$handle['current']];
            } else {
                $handle = &$this->_attributeBucket;
                $data   = $handle['data'][$handle['current']][$handle['branche']];
            }

            if (is_array($data) && isset($data[0])) {
                $data = $data[0];
            }

            $value  = preg_replace_callback(
                '#\(\?(?:p(?:ath)?:)?([^\)]+)\)#',
                function (array $matches) use (&$data) {
                    if (!is_array($data) || !isset($data[$matches[1]])) {
                        return '';
                    }

                    return $data[$matches[1]];
                },
                $value
            );
        }

        // Link.
        if (self::ATTRIBUTE_TYPE_LINK    === $type ||
            self::ATTRIBUTE_TYPE_UNKNOWN === $type) {
            $value = $this->getAbstractElementSuperRoot()->computeLink($value);
        }

        // Formatter.
        if (null !== $name &&
            true === $this->abstract->attributeExists($name . '-formatter')) {
            $value = $this->formatValue($value, $name . '-');
        }

        return $value;
    }

    /**
     * Format an attribute value.
     * Formatter is of the form:
     *     @attr-formatter="functionName"
     * Arguments of functionName are declared as:
     *     @attr-formatter-argumentName="argumentValue"
     *
     *
     * @param   string     $value    Value.
     * @param   string     $name     Name.
     * @return  string
     */
    protected function formatValue($value, $name = null)
    {
        $_formatter = $name . 'formatter';
        $formatter  = $this->abstract->readAttribute($_formatter);
        $arguments  = $this->abstract->readCustomAttributes($_formatter);

        foreach ($arguments as &$argument) {
            $argument = $this->_formatValue(
                $this->computeAttributeValue($argument)
            );
        }

        $reflection   = xcallable($formatter)->getReflection();
        $distribution = [];
        $placeholder  = $this->_formatValue($value);

        foreach ($reflection->getParameters() as $parameter) {
            $name = strtolower($parameter->getName());

            if (true === array_key_exists($name, $arguments)) {
                $distribution[$name] = $arguments[$name];

                continue;
            } elseif (null !== $placeholder) {
                $distribution[$name] = $placeholder;
                $placeholder         = null;
            }
        }

        if ($reflection instanceof \ReflectionMethod) {
            $value = $reflection->invokeArgs(null, $distribution);
        } else {
            $value = $reflection->invokeArgs($distribution);
        }

        return $value;
    }

    /**
     * Format value to a specific type.
     *
     * @param   string  $value    Value.
     * @return  mixed
     */
    protected function _formatValue($value)
    {
        if (ctype_digit($value)) {
            $value = intval($value);
        } elseif (is_numeric($value)) {
            $value = floatval($value);
        } elseif ('true' === $value) {
            $value = true;
        } elseif ('false' === $value) {
            $value = false;
        } elseif ('null' === $value) {
            $value = null;
        }
        // what about constants?

        return $value;
    }

    /**
     * Compute from strings, directly on the output stream.
     *
     * @return  void
     */
    protected function computeFromString($xyl)
    {
        if (0 < count($this)) {
            return null;
        }

        $stringBuffer = new Stringbuffer\ReadWrite();
        $stringBuffer->initializeWith(
            '<?xml version="1.0" encoding="utf-8"?>' .
            '<fragment xmlns="' . \Hoa\Xyl::NAMESPACE_ID . '">' .
            '<snippet id="h"><yield>' . $xyl . '</yield></snippet>' .
            '</fragment>'
        );

        $root     = $this->getAbstractElementSuperRoot();
        $fragment = $root->open($stringBuffer->getStreamName());
        $fragment->render($fragment->getSnippet('h'));

        return;
    }

    /**
     * Check if this is the last iteration or not.
     *
     * @return  bool
     */
    public function isLastIteration()
    {
        return $this->_lastIteration;
    }

    /**
     * Get all declared attributes.
     *
     * @return  array
     */
    protected function getDeclaredAttributes()
    {
        $out      = static::_getDeclaredAttributes();
        $abstract = $this->abstract;

        foreach ($out as $attr => $type) {
            if (self::ATTRIBUTE_TYPE_CUSTOM === $type) {
                foreach ($abstract->readCustomAttributes($attr) as $a => $_) {
                    $out[$attr . '-' . $a] = self::ATTRIBUTE_TYPE_UNKNOWN;
                }
            }
        }

        return $out;
    }

    /**
     * Get all declared attributes in a trivial way.
     *
     * @return  array
     */
    protected static function _getDeclaredAttributes()
    {
        $out    = [];
        $parent = get_called_class();

        do {
            if (!isset($parent::$_attributes)) {
                continue;
            }

            $out = array_merge($out, $parent::$_attributes);
        } while (false !== ($parent = get_parent_class($parent)));

        return $out;
    }

    /**
     * Set visibility.
     *
     * @param   bool    $visibility    Visibility.
     * @return  bool
     */
    public function setVisibility($visibility)
    {
        $old               = $this->_visibility;
        $this->_visibility = $visibility;

        return $old;
    }

    /**
     * Get visibility.
     *
     * @return  bool
     */
    public function getVisibility()
    {
        return $this->_visibility;
    }
}

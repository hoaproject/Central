<?php

/**
 * Hoa.
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2016, Hoa community. All rights reserved.
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
namespace Hoa\Heap;

use Hoa\Consistency;

/**
 * Class \Hoa\Heap.
 *
 * The \Hoa\Heap class allows to store element with advanced options
 * like priority and key.
 * The \Hoa\Heap class implements Iterator and Countable interfaces to iterate
 * marks, or count the number of marks.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
abstract class Heap implements \Countable, \Iterator
{
    /**
     * Where store the element.
     *
     * @var array|\SplObjectStorage
     */
    protected $storage;

    /**
     * Map of hash to priorities.
     *
     * @var array
     */
    protected $priorities = [];

    /**
     * Map of iterator to key.
     *
     * @var array
     */
    protected $traversable = [];

    /**
     * Position of iterator traversable.
     *
     * @var int
     */
    protected $cursor = 0;

    /**
     * Method for compare two element and determine the
     * position inside the heap.
     *
     * @param $key1
     * @param $key2
     *
     * @return mixed
     */
    abstract protected function compare($key1, $key2);

    /**
     * Sort heap using the compare method.
     */
    public function sort()
    {
        usort($this->traversable, [$this, 'compare']);
    }

    /**
     * Insert new element on the Heap.
     *
     * @param mixed $value
     * @param mixed $priority
     * @param mixed $key
     *
     * @throws Exception
     *
     * @return string key of element
     */
    public function insert($value, $priority = 0, $key = null)
    {
        if (null === $key) {
            $key = Consistency::uuid();
        }

        if (true === isset($this->priorities[$key])) {
            throw new Exception('Key already exists, must be unique');
        }

        $this->storage[$key] = $value;

        $this->traversable[]    = $key;
        $this->priorities[$key] = $priority;

        return $key;
    }

    /**
     * Detach element from his key.
     *
     * This function will rewind the iterator after use.
     *
     * @param string $key
     *
     * @return mixed element
     */
    public function detach($key)
    {
        if (false === isset($this->priorities[$key])) {
            throw new KeyNotFoundException('Given key does not exist in heap');
        }

        $traversableHashMap = array_flip($this->traversable);
        $cursor             = $traversableHashMap[$key];
        $element            = $this->storage[$key];

        unset($this->storage[$key]);
        unset($this->traversable[$cursor]);
        unset($this->priorities[$key]);

        $this->rewind();

        return $element;
    }

    /**
     * @param $key
     * @param $element
     *
     * @return bool
     */
    public function extract(&$key, &$element)
    {
        if (false === $this->valid()) {
            return false;
        }

        $key     = $this->key();
        $element = $this->current();

        unset($this->storage[$key]);
        unset($this->traversable[$this->cursor]);
        unset($this->priorities[$key]);

        return true;
    }

    /**
     * Peeks at the element from the top of the heap.
     *
     * @return \Generator
     */
    public function top()
    {
        $this->sort();

        for (;;) {
            $this->rewind(false);

            if (false === $this->extract($key, $element)) {
                return;
            }

            (yield $key => $element);
        }
    }

    /**
     * Peeks at the element from the end of the heap.
     *
     * @return \Generator
     */
    public function pop()
    {
        $this->sort();

        for (;;) {
            $this->end(false);

            if (false === $this->extract($key, $element)) {
                return;
            }

            (yield $key => $element);
        }
    }

    /**
     * Move forward to end of iterator.
     */
    public function end($sort = true)
    {
        if (true === $sort) {
            $this->sort();
        }

        if (0 === $this->count()) {
            $this->cursor = 0;

            return;
        }

        end($this->traversable);
        $this->cursor = key($this->traversable);
        reset($this->traversable);

        return;
    }

    /**
     * Return the current element priority.
     *
     * @return int
     */
    public function priority()
    {
        return $this->priorities[$this->key()];
    }

    /**
     * Return the current element.
     *
     * @return mixed element or false if out of range
     */
    public function current()
    {
        if (false === $this->valid()) {
            return false;
        }

        return $this->storage[$this->key()];
    }

    /**
     * Return the key of the current element.
     *
     * @return string|null if cursor out of range
     */
    public function key()
    {
        if (false === $this->valid()) {
            return;
        }

        return $this->traversable[$this->cursor];
    }

    /**
     * Move forward to next element.
     */
    public function next()
    {
        ++$this->cursor;

        return;
    }

    /**
     * Rewind the iterator to the first element.
     * 
     * @param bool $sort
     */
    public function rewind($sort = true)
    {
        reset($this->traversable);
        $this->cursor = key($this->traversable);

        if (true === $sort) {
            $this->sort();
        }

        return;
    }

    /**
     * Check if current position is valid.
     *
     * @return bool
     */
    public function valid()
    {
        return isset($this->traversable[$this->cursor]);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->traversable);
    }
}

/*
 * Flex entity.
 */
Consistency::flexEntity('Hoa\Heap\Heap');

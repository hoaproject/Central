<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2014, Ivan Enderlin. All rights reserved.
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

namespace Hoa\Translate {

/**
 * Class \Hoa\Translate.
 *
 * Generic class for translaters.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin.
 * @license    New BSD License
 */

abstract class Translate implements \IteratorAggregate {

    /**
     * Stream.
     *
     * @var \Hoa\Stream\IStream\In object
     */
    protected $_stream = null;

    /**
     * Messages.
     *
     * @var \Hoa\Translate array
     */
    protected $_data   = array();



    /**
     * Constructor.
     *
     * @access  public
     * @param   \Hoa\Stream\IStream\In  $stream    Stream.
     * @return  void
     */
    public function __construct ( \Hoa\Stream\IStream\In $stream ) {

        $this->setStream($stream);

        return;
    }

    /**
     * Set stream.
     *
     * @access  protected
     * @param   \Hoa\Stream\IStream\In  $stream    Stream.
     * @return  \Hoa\Stream\IStream\In
     */
    protected function setStream ( \Hoa\Stream\IStream\In $stream ) {

        $old           = $this->_stream;
        $this->_stream = $stream;

        return $old;
    }

    /**
     * Get stream.
     *
     * @access  public
     * @return  \Hoa\Stream\IStream\In
     */
    public function getStream ( ) {

        return $this->_stream;
    }

    /**
     * Set messages.
     *
     * @access  protected
     * @param   array  $data    Data.
     * @return  array
     */
    protected function setData ( Array $data ) {

        $old         = $this->_data;
        $this->_data = $data;

        return $old;
    }

    /**
     * Get messages.
     *
     * @access  public
     * @return  array
     */
    public function getData ( ) {

        return $this->_data;
    }

    /**
     * Iterate over messages.
     *
     * @access  public
     * @return  \ArrayIterator
     */
    public function getIterator ( ) {

        return new \ArrayIterator($this->_data);
    }

    /**
     * Get translation for regular message.
     *
     * @access  public
     * @param   string  $message    Message.
     * @param   mixed   …           Parameters.
     * @return  string
     */
    abstract public function _ ( $message );

    /**
     * Get translation for plural messages.
     * Messages are concatenated by NUL (\0), or \0 or ^@. They can be escaped
     * by \.
     *
     * @access  public
     * @param   string  $message    Message.
     * @param   int     $n          n (to select the plural).
     * @param   mixed   …           Parameters.
     * @return  string
     */
    abstract public function _n ( $message, $n );
}

}

namespace {

/**
 * Flex entity.
 */
Hoa\Core\Consistency::flexEntity('Hoa\Translate\Translate');

}

<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2015, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Mail\Content
 */
-> import('Mail.Content.~')

/**
 * \Hoa\Mime
 */
-> import('Mime.~');

}

namespace Hoa\Mail\Content {

/**
 * Class \Hoa\Mail\Content\Attachment.
 *
 * This class represents an attachment.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2015 Ivan Enderlin.
 * @license    New BSD License
 */

class Attachment extends Content {

    /**
     * Stream.
     *
     * @var \Hoa\Stream object
     */
    protected $_stream = null;



    /**
     * Construct an attachment with a stream and a name.
     *
     * @access  public
     * @param   \Hoa\Stream  $stream    Stream that contains the attachment.
     * @param   string       $name      Name of the attachment (if null, will
     *                                  use the stream basename).
     * @return  void
     */
    public function __construct ( \Hoa\Stream $stream, $name = null ) {

        parent::__construct();

        if(null === $name)
            if($stream instanceof \Hoa\Stream\IStream\Pathable)
                $name = $stream->getBasename();
            else
                $name = basename($stream->getStreamName());

        $mime                        = new \Hoa\Mime($stream);
        $this['content-type']        = $mime->getMime() ?: 'application/octet-stream';
        $this['content-disposition'] = 'attachment; ' .
                                       'filename=' . $name . ';';
        $this->setStream($stream);

        return;
    }

    /**
     * Set the stream.
     *
     * @access  protected
     * @param   \Hoa\Stream  $stream    Stream.
     * @return  \Hoa\Stream
     */
    protected function setStream ( \Hoa\Stream $stream ) {

        $old           = $this->_stream;
        $this->_stream = $stream;

        return $old;
    }

    /**
     * Get the stream.
     *
     * @access  public
     * @return  \Hoa\Stream
     */
    public function getStream ( ) {

        return $this->_stream;
    }

    /**
     * Get final “plain” content.
     *
     * @access  protected
     * @return  string
     */
    protected function _getContent ( ) {

        return base64_encode($this->getStream()->readAll());
    }
}

}

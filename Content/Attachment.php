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

namespace Hoa\Mail\Content;

use Hoa\Mime;
use Hoa\Stream;

/**
 * Class \Hoa\Mail\Content\Attachment.
 *
 * This class represents an attachment.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Attachment extends Content
{
    /**
     * Stream.
     *
     * @var \Hoa\Stream
     */
    protected $_stream = null;



    /**
     * Construct an attachment with a stream and a name.
     *
     * @param   \Hoa\Stream  $stream      Stream that contains the attachment.
     * @param   string       $name        Name of the attachment (if null, will
     *                                    use the stream basename).
     * @param   string       $mimeType    Force a MIME type.
     */
    public function __construct(Stream $stream, $name = null, $mimeType = null)
    {
        parent::__construct();

        if (null === $name) {
            if ($stream instanceof Stream\IStream\Pathable) {
                $name = $stream->getBasename();
            } else {
                $name = basename($stream->getStreamName());
            }
        }

        if (null === $mimeType) {
            $mimeType = null;

            try {
                $mime     = new Mime($stream);
                $mimeType = $mime->getMime();
            } catch (Mime\Exception $e) {
            }

            if (null === $mimeType) {
                $mimeType = 'application/octet-stream';
            }
        }

        $size = null;

        if ($stream instanceof Stream\IStream\Statable &&
            false !== $_size = $stream->getSize()) {
            $size = '; size=' . $_size;
        }

        $this['content-type']        = $mimeType;
        $this['content-disposition'] =
            'attachment; filename="' .
            str_replace('"', '-', $name) .
            '"' .
            $size;

        $this->setStream($stream);

        return;
    }

    /**
     * Set the stream.
     *
     * @param   \Hoa\Stream  $stream    Stream.
     * @return  \Hoa\Stream
     */
    protected function setStream(Stream $stream)
    {
        $old           = $this->_stream;
        $this->_stream = $stream;

        return $old;
    }

    /**
     * Get the stream.
     *
     * @return  \Hoa\Stream
     */
    public function getStream()
    {
        return $this->_stream;
    }

    /**
     * Get final “plain” content.
     *
     * @return  string
     */
    protected function _getContent()
    {
        return Encoder\Base64::encode($this->getStream()->readAll());
    }
}

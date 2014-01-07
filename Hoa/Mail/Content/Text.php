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

namespace {

from('Hoa')

/**
 * \Hoa\Mail\Content
 */
-> import('Mail.Content.~');

}

namespace Hoa\Mail\Content {

/**
 * Class \Hoa\Mail\Content\Text.
 *
 * This class represents a text.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin.
 * @license    New BSD License
 */

class Text extends Content {

    /**
     * Content.
     *
     * @var \Hoa\Mail\Content\Text string
     */
    protected $_content = null;



    /**
     * Construct a text content.
     *
     * @access  public
     * @param   string  $content    Content.
     * @return  void
     */
    public function __construct ( $content = null ) {

        parent::__construct();
        $this['content-type'] = 'text/plain; charset=utf-8';
        $this->append($content);

        return;
    }

    /**
     * Prepend content (in memory order, i.e. from left-to-right only).
     *
     * @access  public
     * @param   string  $content    Content.
     * @return  string
     */
    public function prepend ( $content ) {

        $this->_content = $content . $this->_content;

        return $this;
    }

    /**
     * Append content (in memory order, i.e. from left-to-right only).
     *
     * @access  public
     * @param   string  $content    Content.
     * @return  string
     */
    public function append ( $content ) {

        $this->_content .= $content;

        return $this;
    }

    /**
     * Get the content.
     *
     * @access  public
     * @return  string
     */
    public function get ( ) {

        return $this->_content;
    }

    /**
     * Get final “plain” content.
     *
     * @access  protected
     * @return  string
     */
    protected function _getContent ( ) {

        return base64_encode($this->get());
    }
}

}

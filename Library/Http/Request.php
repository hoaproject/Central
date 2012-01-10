<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Http\Exception
 */
-> import('Http.Exception.~')

/**
 * \Hoa\Http
 */
-> import('Http.~');

}

namespace Hoa\Http {

/**
 * Class \Hoa\Http\Request.
 *
 * HTTP request support.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Request extends Http {

    /**
     * Method: CONNECT.
     *
     * @const string
     */
    const METHOD_CONNECT  = 'connect';

    /**
     * Method: DELETE.
     *
     * @const string
     */
    const METHOD_DELETE   = 'delete';

    /**
     * Method: GET.
     *
     * @const string
     */
    const METHOD_GET      = 'get';

    /**
     * Method: HEAD.
     *
     * @const string
     */
    const METHOD_HEAD     = 'head';

    /**
     * Method: OPTIONS.
     *
     * @const string
     */
    const METHOD_OPTIONS  = 'options';

    /**
     * Method: POST.
     *
     * @const string
     */
    const METHOD_POST     = 'post';

    /**
     * Method: PUT.
     *
     * @const string
     */
    const METHOD_PUT      = 'put';

    /**
     * Method: TRACE.
     *
     * @const string
     */
    const METHOD_TRACE    = 'trace';

    /**
     * Other methods.
     *
     * @const string
     */
    const METHOD_EXTENDED = 'extended';

    /**
     * Request method (please, see self::METHOD_* constants).
     *
     * @var \Hoa\Http\Request string
     */
    protected $_method      = null;

    /**
     * Request URL.
     *
     * @var \Hoa\Http\Request string
     */
    protected $_url         = null;

    /**
     * Request HTTP version.
     *
     * @var \Hoa\Http\Request float
     */
    protected $_httpVersion = null;

    /**
     * Request body.
     *
     * @var \Hoa\Http\Request string
     */
    protected $_body        = null;



    /**
     * Parse a HTTP packet.
     *
     * @access  public
     * @param   string  $packet    HTTP packet.
     * @return  void
     * @throw   \Hoa\Http\Exception
     */
    public function parse ( $packet ) {

        $headers     = explode("\r\n", $packet);
        $http        = array_shift($headers);
        $this->_body = null;

        foreach($headers as $i => $header)
            if('' == trim($header)) {

                unset($headers[$i]);
                $this->_body = trim(
                    implode("\r\n", array_splice($headers, $i))
                );
                break;
            }

        if(0 === preg_match('#^([^\s]+)\s+([^\s]+)\s+HTTP/(1\.(?:0|1))$#i', $http, $matches))
            throw new Exception(
                'HTTP headers are not well-formed: %s', 0, $http);

        switch($method = strtolower($matches[1])) {

            case self::METHOD_CONNECT:
            case self::METHOD_DELETE:
            case self::METHOD_GET:
            case self::METHOD_HEAD:
            case self::METHOD_OPTIONS:
            case self::METHOD_POST:
            case self::METHOD_PUT:
            case self::METHOD_TRACE:
                $this->_method = $method;
              break;

            default:
                $this->_method = self::METHOD_EXTENDED;
        }

        $this->_url         = $matches[2];
        $this->_httpVersion = (float) $matches[3];

        $this->_parse($headers);

        return;
    }

    /**
     * Set request method.
     *
     * @access  public
     * @param   string  $method    Method (please, see self::METHOD_*
     *                             constants).
     * @return  string
     */
    public function setMethod ( $method ) {

        $old           = $this->_method;
        $this->_method = $method;

        return $old;
    }

    /**
     * Get request method.
     *
     * @access  public
     * @return  string
     */
    public function getMethod ( ) {

        return $this->_method;
    }

    /**
     * Set request URL.
     *
     * @access  public
     * @param   string  $url    URL.
     * @return  string
     */
    public function setUrl ( $url ) {

        $old        = $this->_url;
        $this->_url = $url;

        return $old;
    }

    /**
     * Get request URL.
     *
     * @access  public
     * @return  string
     */
    public function getUrl ( ) {

        return $this->_url;
    }

    /**
     * Set request HTTP version.
     *
     * @access  public
     * @param   float  $version    HTTP version.
     * @return  float
     */
    public function setHttpVersion ( $version ) {

        $old                = $this->_httpVersion;
        $this->_httpVersion = $version;

        return $old;
    }

    /**
     * Get request HTTP version.
     *
     * @access  public
     * @return  float
     */
    public function getHttpVersion ( ) {

        return $this->_httpVersion;
    }

    /**
     * Set request body.
     *
     * @access  public
     * @param   string  $body   Body.
     * @return  string
     */
    public function setBody ( $body ) {

        $old         = $this->_body;
        $this->_body = $body;

        return $old;
    }

    /**
     * Get request body.
     *
     * @access  public
     * @return  string
     */
    public function getBody ( ) {

        return $this->_body;
    }
}

}

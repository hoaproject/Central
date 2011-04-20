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
 * \Hoa\Http\Exception
 */
-> import('Http.Exception');

}

namespace Hoa\Http {

/**
 * Class \Hoa\Http\Request.
 *
 * Parse HTTP headers.
 * Please, see the RFC 2616.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Request {

    const METHOD_CONNECT  =   0;
    const METHOD_DELETE   =   1;
    const METHOD_GET      =   2;
    const METHOD_HEAD     =   4;
    const METHOD_OPTIONS  =   8;
    const METHOD_POST     =  16;
    const METHOD_PUT      =  32;
    const METHOD_TRACE    =  64;
    const METHOD_EXTENDED = 128;

    protected $_method             = null;
    protected $_url                = null;
    protected $_httpVersion        = null;
    protected $_content            = null;

    protected $_accept             = null;
    protected $_acceptCharset      = null;
    protected $_acceptEncoding     = null;
    protected $_acceptLanguage     = null;
    protected $_authorization      = null;
    protected $_connection         = null;
    protected $_except             = null;
    protected $_from               = null;
    protected $_host               = null;
    protected $_ifMatch            = null;
    protected $_ifModifiedSince    = null;
    protected $_ifNoneMatch        = null;
    protected $_ifRange            = null;
    protected $_ifUnmodifiedSince  = null;
    protected $_keepAlive          = null;
    protected $_maxForwards        = null;
    protected $_proxyAuthorization = null;
    protected $_range              = null;
    protected $_referer            = null;
    protected $_te                 = null;
    protected $_userAgent          = null;

    public function __construct ( $headers = null ) {

        if(null !== $headers)
            $this->parse($headers);

        return;
    }

    public function parse ( $headers ) {

        $this->reset();
        $headers = explode("\r\n", $headers);
        $http    = array_shift($headers);

        while(   !empty($headers)
              && '' == $handle = trim(array_pop($headers)));

        $this->setContent($handle);
        array_pop($headers);

        if(0 === preg_match('#^([^\s]+)\s+([^\s]+)\s+HTTP/(1\.(0|1))$#', $http, $matches))
            throw new Exception(
                'HTTP headers are not well-formed: %s.', 0, $http);

        switch(strtolower($matches[1])) {

            case 'connect':
                $this->_method = self::METHOD_CONNECT;
              break;

            case 'delete':
                $this->_method = self::METHOD_DELETE;
              break;

            case 'get':
                $this->_method = self::METHOD_GET;
              break;

            case 'head':
                $this->_method = self::METHOD_HEAD;
              break;

            case 'options':
                $this->_method = self::METHOD_OPTIONS;
              break;

            case 'post':
                $this->_method = self::METHOD_POST;
              break;

            case 'put':
                $this->_method = self::METHOD_PUT;
              break;

            case 'trace':
                $this->_method = self::METHOD_TRACE;
              break;

            default:
                $this->_method = self::METHOD_EXTENDED;
        }

        $this->setURL($matches[2]);
        $this->setHTTPVersion((float) $matches[3]);

        foreach($headers as $header) {

            $semicolon  = strpos($header, ':');
            $fieldName  = strtolower(trim(substr($header, 0, $semicolon)));
            $fieldValue = trim(substr($header, $semicolon + 1));

            switch($fieldName) {

                case 'accept':
                    $this->setAccept($fieldValue);
                  break;

                case 'accept-charset':
                    $this->setAcceptCharset($fieldValue);
                  break;

                case 'accept-encoding':
                    $this->setAcceptEncoding($fieldValue);
                  break;

                case 'accept-language':
                    $this->setAcceptLanguage($fieldValue);
                  break;

                case 'authorization':
                    $this->setAuthorization($fieldValue);
                  break;

                case 'connection':
                    $this->setConnection($fieldValue);
                  break;

                case 'except':
                    $this->setExcept($fieldValue);
                  break;

                case 'from':
                    $this->setFrom($fieldValue);
                  break;

                case 'host':
                    $this->setHost($fieldValue);
                  break;

                case 'if-match':
                    $this->setIfMatch($fieldValue);
                  break;

                case 'if-modified-since':
                    $this->setIfModifiedSince($fieldValue);
                  break;

                case 'if-none-match':
                    $this->setIfNoneMatch($fieldValue);
                  break;

                case 'if-range':
                    $this->setIfRange($fieldValue);
                  break;

                case 'if-unmodified-since':
                    $this->setIfUnmodifiedSince($fieldValue);
                  break;

                case 'keep-alive':
                    $this->setKeepAlive($fieldValue);
                  break;

                case 'max-forwards':
                    $this->setMaxForwards($fieldValue);
                  break;

                case 'proxy-authorization':
                    $this->setProxyAuthorization($fieldValue);
                  break;

                case 'range':
                    $this->setRange($fieldValue);
                  break;

                case 'referer':
                    $this->setReferer($fieldValue);
                  break;

                case 'te':
                    $this->setTE($fieldValue);
                  break;

                case 'user-agent':
                    $this->setUserAgent($fieldValue);
                  break;

                default:
                    //var_dump($fieldName, $fieldValue);
                    //echo "\n";
            }
        }

        return;
    }

    public function reset ( ) {

        $this->_method             = null;
        $this->_url                = null;
        $this->_httpVersion        = null;
        $this->_content            = null;
        $this->_accept             = null;
        $this->_acceptCharset      = null;
        $this->_acceptEncoding     = null;
        $this->_acceptLanguage     = null;
        $this->_authorization      = null;
        $this->_connection         = null;
        $this->_except             = null;
        $this->_from               = null;
        $this->_host               = null;
        $this->_ifMatch            = null;
        $this->_ifModifiedSince    = null;
        $this->_ifNoneMatch        = null;
        $this->_ifRange            = null;
        $this->_ifUnmodifiedSince  = null;
        $this->_keepAlive          = null;
        $this->_maxForwards        = null;
        $this->_proxyAuthorization = null;
        $this->_range              = null;
        $this->_referer            = null;
        $this->_te                 = null;
        $this->_userAgent          = null;

        return;
    }

    public function setMethod ( $method ) {

        $old           = $this->_method;
        $this->_method = $method;

        return $old;
    }

    public function getMethod ( ) {

        return $this->_method;
    }

    public function getMethodAsString ( ) {

        switch($this->getMethod()) {

            case self::METHOD_CONNECT:
                return 'connect';
              break;

            case self::METHOD_DELETE:
                return 'delete';
              break;

            case self::METHOD_GET:
                return 'get';
              break;

            case self::METHOD_HEAD:
                return 'head';
              break;

            case self::METHOD_OPTIONS:
                return 'options';
              break;

            case self::METHOD_POST:
                return 'post';
              break;

            case self::METHOD_PUT:
                return 'put';
              break;

            case self::METHOD_TRACE:
                return 'trace';
              break;

            case self::METHOD_EXTENDED:
            default:
                return 'extended';
              break;

        }
    }

    public function setContent ( $content ) {

        if(empty($content))
            $content = null;

        $old            = $this->_content;
        $this->_content = trim($content);

        return $old;
    }

    public function getContent ( ) {

        return $this->_content;
    }

    public function setAccept ( $accept ) {

        $old           = $this->_accept;
        $this->_accept = $accept;

        return $old;
    }

    public function getAccept ( ) {

        return $this->_accept;
    }

    public function setAcceptCharset ( $acceptCharset ) {

        $old                  = $this->_acceptCharset;
        $this->_acceptCharset = $acceptCharset;

        return $old;
    }

    public function getAcceptCharset ( ) {

        return $this->_acceptCharset;
    }

    public function setAcceptEncoding ( $acceptEncoding ) {

        $old                   = $this->_acceptEncoding;
        $this->_acceptEncoding = $acceptEncoding;

        return $old;
    }

    public function getAcceptEncoding ( ) {

        return $this->_acceptEncoding;
    }

    public function setAcceptLanguage ( $acceptLanguage ) {

        $old                   = $this->_acceptLanguage;
        $this->_acceptLanguage = $acceptLanguage;

        return $old;
    }

    public function getAcceptLanguage ( ) {

        return $this->_acceptLanguage;
    }

    public function setAuthorization ( $authorization ) {

        $old                  = $this->_authorization;
        $this->_authorization = $authorization;

        return $old;
    }

    public function getAuthorization ( ) {

        return $this->_authorization;
    }

    public function setConnection ( $connection ) {

        $old               = $this->_connection;
        $this->_connection = $connection;

        return $old;
    }

    public function getConnection ( ) {

        return $this->_connection;
    }

    public function setExcept ( $except ) {

        $old           = $this->_except;
        $this->_except = $except;

        return $old;
    }

    public function getExcept ( ) {

        return $this->_except;
    }

    public function setFrom ( $from ) {

        $old         = $this->_from;
        $this->_from = $from;

        return $old;
    }

    public function getFrom ( ) {

        return $this->_from;
    }

    public function setHost ( $host ) {

        $old         = $this->_host;
        $this->_host = $host;

        return $old;
    }

    public function getHost ( ) {

        return $this->_host;
    }

    public function setHTTPVersion ( $version ) {

        $old                = $this->_httpVersion;
        $this->_httpVersion = $version;

        return $old;
    }

    public function getHTTPVersion ( ) {

        return $this->_httpVersion;
    }

    public function setIfMatch ( $ifMatch ) {

        $old            = $this->_ifMatch;
        $this->_ifMatch = $ifMatch;

        return $old;
    }

    public function getIfMatch ( ) {

        return $this->_ifMatch;
    }

    public function setIfModifiedSince ( $ifModifiedSince ) {

        $old                    = $this->_ifModifiedSince;
        $this->_ifModifiedSince = $ifModifiedSince;

        return $old;
    }

    public function getIfModifiedSince ( ) {

        return $this->_ifModifiedSince;
    }

    public function setIfNoneMatch ( $ifNoneMatch ) {

        $old                = $this->_ifNoneMatch;
        $this->_ifNoneMatch = $ifNoneMatch;

        return $old;
    }

    public function getIfNoneMatch ( ) {

        return $this->_ifNoneMatch;
    }

    public function setIfRange ( $ifRange ) {

        $old            = $this->_ifRange;
        $this->_ifRange = $ifRange;

        return $old;
    }

    public function getIfRange ( ) {

        return $this->_ifRange;
    }

    public function setIfUnmodifiedSince ( $ifUnmodifiedSince ) {

        $old                      = $this->_ifUnmodifiedSince;
        $this->_ifUnmodifiedSince = $ifUnmodifiedSince;

        return $old;
    }

    public function getIfUnmodifiedSince ( ) {

        return $this->_ifUnmodifiedSince;
    }

    public function setKeepAlive ( $keepAlive ) {

        $old              = $this->_keepAlive;
        $this->_keepAlive = $keepAlive;

        return $old;
    }

    public function getKeepAlive ( ) {

        return $this->_keepAlive;
    }

    public function setMaxForwards ( $maxForwards ) {

        $old                = $this->_maxForwards;
        $this->_maxForwards = $maxForwards;

        return $old;
    }

    public function getMaxForwards ( ) {

        return $this->_maxForwards;
    }

    public function setProxyAuthorization ( $proxyAuthorization ) {

        $old                       = $this->_proxyAuthorization;
        $this->_proxyAuthorization = $proxyAuthorization;

        return $old;
    }

    public function getProxyAuthrization ( ) {

        return $this->_proxyAuthorization;
    }

    public function setRange ( $range ) {

        $old          = $this->_range;
        $this->_range = $range;

        return $old;
    }

    public function getRange ( ) {

        return $this->_range;
    }

    public function setReferer ( $referer ) {

        $old            = $this->_referer;
        $this->_referer = $referer;

        return $old;
    }

    public function getReferer ( ) {

        return $this->_referer;
    }

    public function setTE ( $te ) {

        $old       = $this->_te;
        $this->_te = $te;

        return $old;
    }

    public function getTE ( ) {

        return $this->_te;
    }

    public function setURL ( $url ) {

        $old        = $this->_url;
        $this->_url = ltrim($url, '/');

        return $old;
    }

    public function getURL ( ) {

        return $this->_url;
    }

    public function setUserAgent ( $userAgent ) {

        $old              = $this->_userAgent;
        $this->_userAgent = $userAgent;

        return $old;
    }

    public function getUserAgent ( ) {

        return $this->_userAgent;
    }
}

}

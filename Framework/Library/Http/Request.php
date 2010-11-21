<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 * @category    Framework
 * @package     Hoa_Http
 * @subpackage  Hoa_Http_Request
 *
 */

/**
 * Hoa_Http_Exception
 */
import('Http.Exception');

/**
 * Class Hoa_Http_Request.
 *
 * Parse HTTP headers.
 * Please, see the RFC 2616.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Http
 * @subpackage  Hoa_Http_Request
 */

class Hoa_Http_Request {

    const METHOD_CONNECT  =   0;
    const METHOD_DELETE   =   1;
    const METHOD_GET      =   2;
    const METHOD_HEAD     =   4;
    const METHOD_OPTIONS  =   8;
    const METHOD_POST     =  16;
    const METHOD_PUT      =  32;
    const METHOD_TRACE    =  64;
    const METHOD_EXTENDED = 128;

    protected $_method      = null;
    protected $_url         = null;
    protected $_httpVersion = null;

    protected $_accept             = null;
    protected $_acceptCharset      = null;
    protected $_acceptEncoding     = null;
    protected $_acceptLanguage     = null;
    protected $_authorization      = null;
    protected $_connection         = null;
    protected $_except             = null;
    protected $_from               = null;
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

        $headers = explode("\r\n", $headers);
        $http    = array_shift($headers);
        array_pop($headers);
        array_pop($headers);

        if(0 === preg_match('#^(\w+)\s*([^\s*]+)\s*HTTP/(1\..?)$#', $http, $matches))
            throw new Hoa_Http_Exception(
                'HTTP headers are not well-formed: %', 0, $http);

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

        $this->_url         = $this->setURL($matches[2]);
        $this->_httpVersion = $this->setHTTPVersion((float) $matches[3]);

        foreach($headers as $header) {

            $semicolon  = strpos($header, ':');
            $fieldName  = strtolower(substr($header, 0, $semicolon));
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

    public function setMethod ( $method ) {

        $old           = $this->_method;
        $this->_method = $method;

        return $old;
    }

    public function getMethod ( ) {

        return $this->_method;
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
        $this->_url = $url;

        return;
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

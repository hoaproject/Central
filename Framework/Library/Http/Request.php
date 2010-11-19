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
 * Hoa_Core
 */
require_once 'Core.php';

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
                'HTTP headers are not well-formed.', 0);

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

        $this->_url         = $matches[2];
        $this->_httpVersion = (float) $matches[3];

        foreach($headers as $header) {

            $semicolon  = strpos($header, ':');
            $fieldName  = strtolower(substr($header, 0, $semicolon));
            $fieldValue = trim(substr($header, $semicolon + 1));

            switch($fieldName) {

                case 'host':
                    $this->setHost($fieldValue);
                  break;

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

                default:
                    var_dump($fieldName, $fieldValue);
                    echo "\n";
            }
        }

        return;
    }

    public function setMethod ( $method ) {

        $old           = $this->_method;
        $this->_method = $method;

        return $old;
    }

    public function setHost ( $host ) {

        
    }
}

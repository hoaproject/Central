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
 * @subpackage  Hoa_Http_Response
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
 * Hoa_Stream_Interface_Out
 */
import('Stream.Interface.Out');

/**
 * Class Hoa_Http_Response.
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
 * @subpackage  Hoa_Http_Response
 */

class Hoa_Http_Response implements Hoa_Stream_Interface_Out {

    /**
     * Continue.
     *
     * @const int
     */
    const STATUS_CONTINUE                        = 100;

    /**
     * Switching protocols.
     *
     * @const int
     */
    const STATUS_SWITCHING_PROTOCOLS             = 101;

    /**
     * OK.
     *
     * @const int
     */
    const STATUS_OK                              = 200;

    /**
     * Created.
     *
     * @const int
     */
    const STATUS_CREATED                         = 201;

    /**
     * Accepted.
     *
     * @const int
     */
    const STATUS_ACCEPTED                        = 202;

    /**
     * Non-authoritative information.
     *
     * @const int
     */
    const STATUS_NON_AUTHORITATIVE_INFORMATION   = 203;

    /**
     * No content.
     *
     * @const int
     */
    const STATUS_NO_CONTENT                      = 204;

    /**
     * Reset content.
     *
     * @const int
     */
    const STATUS_RESET_CONTENT                   = 205;

    /**
     * Partial content.
     *
     * @const int
     */
    const STATUS_PARTIAL_CONTENT                 = 206;

    /**
     * Multiple choices.
     *
     * @const int
     */
    const STATUS_MULTIPLE_CHOICES                = 300;

    /**
     * Moved permanently.
     *
     * @const int
     */
    const STATUS_MOVED_PERMANENTLY               = 301;

    /**
     * Found.
     *
     * @const int
     */
    const STATUS_FOUND                           = 302;

    /**
     * See other.
     *
     * @const int
     */
    const STATUS_SEE_OTHER                       = 303;

    /**
     * Not modified.
     *
     * @const int
     */
    const STATUS_NOT_MODIFIED                    = 304;

    /**
     * Use proxy.
     *
     * @const int
     */
    const STATUS_USE_PROXY                       = 305;

    /**
     * Temporary redirect.
     *
     * @const int
     */
    const STATUS_TEMPORARY_REDIRECT              = 307;

    /**
     * Bad request.
     *
     * @const int
     */
    const STATUS_BAD_REQUEST                     = 400;

    /**
     * Unauthorized.
     *
     * @const int
     */
    const STATUS_UNAUTHORIZED                    = 401;

    /**
     * Payment required.
     *
     * @const int
     */
    const STATUS_PAYMENT_REQUIRED                = 402;

    /**
     * Forbidden.
     *
     * @const int
     */
    const STATUS_FORBIDDEN                       = 403;

    /**
     * Not found.
     *
     * @const int
     */
    const STATUS_NOT_FOUND                       = 404;

    /**
     * Method not allowed.
     *
     * @const int
     */
    const STATUS_METHOD_NOT_ALLOWED              = 405;

    /**
     * Not acceptable.
     *
     * @const int
     */
    const STATUS_NOT_ACCEPTABLE                  = 406;

    /**
     * Proxy authentification required.
     *
     * @const int
     */
    const STATUS_PROXY_AUTHENTIFICATION_RQEUIRED = 407;

    /**
     * Request time-out.
     *
     * @const int
     */
    const STATUS_REQUEST_TIME_OUT                = 408;

    /**
     * Conflict.
     *
     * @const int
     */
    const STATUS_CONFLICT                        = 409;

    /**
     * Gone.
     *
     * @const int
     */
    const STATUS_GONE                            = 410;

    /**
     * Length required.
     *
     * @const int
     */
    const STATUS_LENGTH_REQUIRED                 = 411;

    /**
     * Precondition failed.
     *
     * @const int
     */
    const STATUS_PRECONDITION_FAILED             = 412;

    /**
     * Request entity too large.
     *
     * @const int
     */
    const STATUS_REQUEST_ENTITY_TOO_LARGE        = 413;

    /**
     * Request URI too large.
     *
     * @const int
     */
    const STATUS_REQUEST_URI_TOO_LARGE           = 414;

    /**
     * Unsupported media type.
     *
     * @const int
     */
    const STATUS_UNSUPPORTED_MEDIA_TYPE          = 415;

    /**
     * Requested range not satisfiable.
     *
     * @const int
     */
    const STATUS_REQUESTED_RANGE_NOT_SATISFIABLE = 416;

    /**
     * Expectation failed.
     *
     * @const int
     */
    const STATUS_EXPECTATION_FAILED              = 417;

    /**
     * Internal server error.
     *
     * @const int
     */
    const STATUS_INTERNAL_SERVER_ERROR           = 500;

    /**
     * Not implemented.
     *
     * @const int
     */
    const STATUS_NOT_IMPLEMENTED                 = 501;

    /**
     * Bad gateway.
     *
     * @const int
     */
    const STATUS_BAD_GATEWAY                     = 502;

    /**
     * Service unavailable.
     *
     * @const int
     */
    const STATUS_SERVICE_UNAVAILABLE             = 503;

    /**
     * Gateway time-out.
     *
     * @const int
     */
    const STATUS_GATEWAY_TIME_OUT                = 504;

    /**
     * HTTP version not supported.
     *
     * @const int
     */
    const STATUS_HTTP_VERSION_NOT_SUPPORTED      = 505;

    protected $_body;

    public function __construct ( $headers = null ) {

        if(null !== $headers)
            $this->parse($headers);

        return;
    }

    public function parse ( $headers ) {

        $headers = explode("\r\n", $headers);
        $state   = 0;

        foreach($headers as $header) {

            $semicolon  = strpos($header, ':');
            $fieldName  = strtolower(substr($header, 0, $semicolon));
            $fieldValue = trim(substr($header, $semicolon + 1));

            switch($fieldName) {

                case 'accept-ranges':
                    $this->setAcceptRanges($fieldValue);
                  break;

                case 'age':
                    $this->setAge($fieldValue);
                  break;

                case 'etag':
                    $this->setETag($fieldValue);
                  break;

                case 'location':
                    $this->setLocation($fieldValue);
                  break;

                case 'proxy-authenticate':
                    $this->setProxyAuthenticate($fieldValue);
                  break;

                case 'retry-after':
                    $this->setRetryAfter($fieldValue);
                  break;

                case 'server':
                    $this->setServer($fieldValue);
                  break;

                case 'vary':
                    $this->setVary($fieldValue);
                  break;

                case 'www-authenticate':
                    $this->setWWWAuthenticate($fieldValue);
                  break;

                case false:
                    if(0 === $state)
                        $state = 1;
                    else
                        $this->setContent($header);
                  break;

                default:
                    var_dump($fieldName, $fieldValue);
                    echo "\n";
            }
        }

        return;
    }

    /**
     * Write n characters.
     *
     * @access  public
     * @param   string  $string    String.
     * @param   int     $length    Length.
     * @return  mixed
     */
    public function write ( $string, $length ) {

        echo $string;
    }

    /**
     * Write a string.
     *
     * @access  public
     * @param   string  $string    String.
     * @return  mixed
     */
    public function writeString ( $string ) {

        echo $string;
    }

    /**
     * Write a character.
     *
     * @access  public
     * @param   string  $character    Character.
     * @return  mixed
     */
    public function writeCharacter ( $character ) {

        echo $string;
    }

    /**
     * Write a boolean.
     *
     * @access  public
     * @param   bool    $boolean    Boolean.
     * @return  mixed
     */
    public function writeBoolean ( $boolean ) {

        echo $boolean;
    }

    /**
     * Write an integer.
     *
     * @access  public
     * @param   int     $integer    Integer.
     * @return  mixed
     */
    public function writeInteger ( $integer ) {

        echo $integer;
    }

    /**
     * Write a float.
     *
     * @access  public
     * @param   float   $float    Float.
     * @return  mixed
     */
    public function writeFloat ( $float ) {

        echo $float;
    }

    /**
     * Write an array.
     *
     * @access  public
     * @param   array   $array    Array.
     * @return  mixed
     */
    public function writeArray ( Array $array ) {

        echo $array;
    }

    /**
     * Write a line.
     *
     * @access  public
     * @param   string  $line    Line.
     * @return  mixed
     */
    public function writeLine ( $line ) {

        echo $line;
    }

    /**
     * Write all, i.e. as much as possible.
     *
     * @access  public
     * @param   string  $string    String.
     * @return  mixed
     */
    public function writeAll ( $string ) {

        echo $string;
    }

    /**
     * Truncate a file to a given length.
     *
     * @access  public
     * @param   int     $size    Size.
     * @return  bool
     */
    public function truncate ( $size ) {

        echo $size;
    }
}

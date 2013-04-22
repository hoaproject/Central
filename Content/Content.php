<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2013, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Mail\Exception\Security
 */
-> import('Mail.Exception.Security');

}

namespace Hoa\Mail\Content {

/**
 * Class \Hoa\Mail\Content.
 *
 * Abstract message content.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

abstract class Content implements \ArrayAccess {

    /**
     * Headers.
     *
     * @var \Hoa\Mail\Content array
     */
    protected $_headers = array();



    /**
     * Construct a generic content.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $this['content-transfer-encoding'] = 'base64';
        $this['content-disposition']       = 'inline';

        return;
    }

    /**
     * Check whether a header is defined.
     *
     * @access  public
     * @param   string  $header    Header.
     * @return  bool
     */
    public function offsetExists ( $header ) {

        $header = mb_strtolower($header);

        return true === array_key_exists($header, $this->_headers);
    }

    /**
     * Get a specific header.
     *
     * @access  public
     * @param   string  $header    Header.
     * @return  string
     */
    public function offsetGet ( $header ) {

        $header = mb_strtolower($header);

        if(false === $this->offsetExists($header))
            return null;

        return $this->_headers[$header];
    }

    /**
     * Set a header.
     *
     * @access  public
     * @param   string  $header    Header.
     * @param   string  $value     Value.
     * @return  string
     */
    public function offsetSet ( $header, $value ) {

        $header = mb_strtolower($header);

        if(true === $this->offsetExists($header))
            $old = $this->_headers[$header];
        else
            $old = null;

        if(0 !== preg_match('#[' . CRLF . ']#', $value))
            throw new \Hoa\Mail\Exception\Security(
                'Header “%s” contains illegal character.', 0, $header);

        $this->_headers[$header] = $value;

        return $old;
    }

    /**
     * Unset a header.
     *
     * @access  public
     * @param   string  $header    Header.
     * @return  void
     */
    public function offsetUnset ( $header ) {

        $header = mb_strtolower($header);
        unset($this->_headers[$header]);

        return;
    }

    /**
     * Get all headers.
     *
     * @access  public
     * @return  array
     */
    public function getHeaders ( ) {

        return $this->_headers;
    }

    /**
     * Get final “plain” content.
     *
     * @access  protected
     * @return  string
     */
    abstract protected function _getContent ( );

    /**
     * Get final formatted content.
     *
     * @access  public
     * @param   bool  $headers    With headers or not.
     * @return  string
     */
    public function getFormattedContent ( $headers = true ) {

        $out = null;

        if(true === $headers)
            $out .= static::formatHeaders($this->getHeaders()) . CRLF;

        $content = $this->_getContent();

        if('base64' === $this['content-transfer-encoding'])
            $content = trim(chunk_split($content, 76, CRLF));

        $out .= $content;

        return $out;
    }

    /**
     * Get formatted headers.
     *
     * @access  public
     * @param   array  $headers    Headers.
     * @return  string
     */
    public static function formatHeaders ( Array $headers ) {

        $out = null;

        foreach($headers as $header => $value) {

            /*
            $value = preg_replace_callback(
                '#(?<value>[^<]+)(?<tail><[^>]+>)#',
                function ( Array $matches ) {

                    return static::qPrintEncode($matches['value']) .
                           $matches['tail'];
                },
                $value
            );
            */

            $out .= $header . ': ' . $value . CRLF;
        }

        return $out;
    }

    /**
     * Encode UTF-8 to quoted-printable format.
     *
     * @access  public
     * @param   string  $string    String to encode.
     * @return  string
     */
    public static function qPrintEncode ( $string ) {

        if(0 === preg_match('#[\x80-\xff]+#', $string))
            return $string;

        return '=?utf-8?Q?' . preg_replace_callback(
            '#[\x80-\xff]+#',
            function ( $matches ) {

                $substring = $matches[0];
                $out       = null;

                for($i = 0, $max = strlen($substring); $i < $max; ++$i)
                    $out .= '=' . dechex(ord($substring[$i]));

                return strtoupper($out);
            },
            $string
        ) . '?=';
    }

    /**
     * Extract address from a contact string, such as:
     *     Gordon Freeman <gordon@freeman.net>
     * or
     *     <gordon@freeman.net>
     * We will get gordon@freeman.net.
     *
     * @access  public
     * @return  string
     */
    public static function getAddress ( $contact ) {

        if(0 !== preg_match('#[^<]*<(?<address>[^>]+)#', $contact, $match))
            return $match['address'];

        return trim($contact);
    }

    /**
     * Get domain of an adress from a contact string.
     * With the example of self::getAddress, we will get freeman.net.
     *
     * @access  public
     * @return  string
     */
    public static function getDomain ( $contact ) {

        $address = static::getAddress($contact);

        if(false !== $pos = strpos($address, '@'))
            return substr($address, $pos + 1);

        return $address;
    }

    /**
     * Transform this object as a string.
     * Alias of $this->getFormattedContent().
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return $this->getFormattedContent();
    }
}

}

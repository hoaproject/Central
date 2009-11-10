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
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Uri
 * @subpackage  Hoa_Uri_Http
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Uri
 */
import('Uri.~');

/**
 * Class Hoa_Uri_Http.
 *
 * Manage HyperText Transfer Protocol of Uniform Resource Identifier.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Uri
 * @subpackage  Hoa_Uri_Http
 */

class Hoa_Uri_Http extends Hoa_Uri {

    /**
     * URI components.
     *
     * @var Hoa_Uri_Http string
     */
    protected $scheme    = 'http';
    protected $username  = '';
    protected $password  = '';
    protected $authority = '';
    protected $port      = '';
    protected $path      = '';
    protected $query     = '';
    protected $fragment  = '';

    /**
     * URI generic syntax.
     *
     * @var Hoa_Uri_Http array
     */
    protected $pattern = array();



    /**
     * __construct
     * Affect and parse $this->_scheme.
     * Prepare generic syntax according to RFC 2396, and 3986.
     * We consider grammar of URI as :
     *     URI = scheme ":" hier-part [ "?" query ] [ "#" fragment ]
     *
     * @access  public
     * @param   uri     string    Specific scheme.
     * @return  void
     * @throw   Hoa_Uri_Exception
     */
    public function __construct ( $uri = null ) {

        $this->_uri   = $uri;
        $this->scheme = $uri;

        // (summed up in "Appendix A.  Collected (A)BNF for URI")

        // Define generic syntax

        $this->pattern['alpha']         = '[a-zA-Z]';
        $this->pattern['digit']         = '[0-9]';
        $this->pattern['alphanum']      = $this->pattern['alpha'] . '|' .
                                          $this->pattern['digit'];

        // mark                         = "-" / "_" / "." / "!" / "~" / "*" / "'"
        //                               / "(" / ")"
        $this->pattern['mark']          = "[-_.!~*'/()]";

        // gen-delims                   = ":" / "/" / "?" / "#" / "[" / "]" / "@"
        $this->pattern['gen-delims']    = '[:/?\#\[\]@]';

        // sub-delims                   = "!" / "$" / "&" / "'" / "(" / ")"
        //                                / "*" / "+" / "," / ";" / "="
        $this->pattern['sub-delims']    = "[!$&'()*+,;=]";

        // reserved                     = gen-delims / sub-delims
        $this->pattern['reserved']      = '(?:' .
                                          $this->pattern['gen-delims'] . '|' .
                                          $this->pattern['sub-delims'] .
                                          ')';

        // unreserved                   = ALPHA / DIGIT / "-" / "." / "_" / "~"
        $this->pattern['unreserved']    = '(?:' .
                                         $this->pattern['alphanum'] . '|' .
                                         '[-_.~])';
        // pct-encoded                  = "%" HEXDIG HEXDIG
        $this->pattern['pct-encoded']   = '(?:%[\da-fA-F]{2})';

        // uric                         = unreserved / pct-encoded / ";" / "?" / ":"
        //                                / "@" / "&" / "=" / "+" / "$" / "," / "/"
        $this->pattern['uric']          = '(?:' .
                                          $this->pattern['unreserved'] . '|' .
                                          $this->pattern['pct-encoded'] . '|' .
                                          '[;?:@&=+$,/])';

        // pchar                        = unreserved / pct-encoded / sub-delims
        //                                / ":" / "@"
        $this->pattern['pchar']         = '(?:' .
                                          $this->pattern['unreserved'] . '|' .
                                          $this->pattern['pct-encoded'] . '|' .
                                          $this->pattern['sub-delims'] . '|' .
                                          '[:@])';

        // segment                      = *pchar
        $this->pattern['segment']       = '(?:' .
                                          $this->pattern['pchar'] .
                                          ')';

        // segment-nz                   = 1*pchar
        $this->pattern['segment-nz']    = '(?:(?:' .
                                          $this->pattern['pchar'] .
                                          ')*)';

        // path-absolute                = "/" [ segment-nz *( "/" segment ) ]
        $this->pattern['path-absolute'] = '(?:/' .
                                          $this->pattern['segment-nz'] . '|' .
                                          '/' . $this->pattern['segment'] . '|' .
                                          ')';

        // reg-name                     = *( unreserved / pct-encoded / sub-delims )
        $this->pattern['reg-name']      = '(?:' .
                                          $this->pattern['unreserved'] . '|' .
                                          $this->pattern['pct-encoded'] . '|' .
                                          $this->pattern['sub-delims'] .
                                          ')';

        if($uri != 'http')
            foreach($this->parse() as $var => $value)
                $this->{$var} = $value;
    }

    /**
     * parse
     * Parse an URI.
     * We considered this follow URI :
     *     http://username:password@domain.tld:80/path/to/file.php?query=value#fragment
     * We start to parse protocol, authority (=host), path, query and anchor on URI.
     * And after, we parse username, password, domain and port on authority.
     *
     * @access  public
     * @param   uri     string    Specific scheme.
     * @return  array
     * @throw   Hoa_Uri_Exception
     */
    public function parse ( $uri = '' ) {

        if(empty($uri))
            $uri = $this->_uri;

        // Parsing a URI :
        //     scheme://authority/path/to/file.php?query=value#fragment
        $pattern = '#^(?:([^:/?\#]+):)?(?://([^/?\#]*))?([^?\#]*)(?:\?([^\#]*))?(?:\#(.*))?#';

        if(false === @preg_match($pattern, $uri, $matches))
           throw new Hoa_Uri_Exception('Scheme match failed : %s', 0, $uri);

        $parse = array(
            'scheme'    => isset($matches[1]) ? $matches[1] : $this->scheme,
            'username'  => '',
            'password'  => '',
            'authority' => isset($matches[2]) ? $matches[2] : '',
            'port'      => '',
            'path'      => isset($matches[3]) ? $matches[3] : '',
            'query'     => isset($matches[4]) ? $matches[4] : '',
            'fragment'  => isset($matches[5]) ? $matches[5] : ''
        );

        // Additional decomposition pattern :
        //     username:password@domain.tld:port
        $pattern = '#^(?:([^:@]+):([^@]+)@)?([^:]+)(?::(.*))?$#';

        if(false === @preg_match($pattern, $parse['authority'], $matches))
            throw new Hoa_Uri_Exception('Authority match failed.', 1, $parse['authority']);

        $parse['username']  = isset($matches[1]) ? $matches[1] : '';
        $parse['password']  = isset($matches[2]) ? $matches[2] : '';
        $parse['authority'] = isset($matches[3]) ? $matches[3] : '';
        $parse['port']      = isset($matches[4]) ? $matches[4] : '';

        return $parse;
    }

    /**
     * getURI
     * Get Uniform Resource Identifier.
     *
     * @access  public
     * @return  string
     * @throw   Hoa_Uri_Exception
     */
    public function getUri ( ) {

        if(empty($this->scheme))
            throw new Hoa_Uri_Exception('Scheme could not be empty.', 2);

        if(empty($this->authority))
            throw new Hoa_Uri_Exception('Authority could not be empty.', 3);

        $out = $this->scheme . '://' .
               (!empty($this->username)
                   ? $this->username . ':' . $this->password . '@'
                   : '') .
               $this->authority .
               (!empty($this->port)
                   ? ':' . $this->port
                   : '') .
               $this->path .
               (!empty($this->query)
                   ? '?' . $this->query
                   : '') .
               (!empty($this->fragment)
                   ? '#' . $this->fragment
                   : '');

        return $out;
    }

    /**
     * isValid
     * Check if an URI is valid.
     * For all isValid* methods, we will check RFC 2396.
     *
     * @access  public
     * @return  bool
     */
    public function isValid ( ) {

        return    $this->validateUsername()
               && $this->validatePassword()
               && $this->validateAuthority()
               && $this->validatePort()
               && $this->validatePath()
               && $this->validateQuery()
               && $this->validateFragment();
    }

    /**
     * _validate
     * Generic method for validate*.
     *
     * @access  protected
     * @param   component  string    Component of URI.
     * @param   value      string    Value of component.
     * @param   pattern    string    Pattern to apply.
     * @param   empty      bool      Empty statement.
     * @return  bool
     * @throw   Hoa_Uri_Exception
     */
    protected function _validate ( $component = null, $value = null,
                                   $pattern   = null, $empty = true ) {

        if($component === null)
            throw new Hoa_Uri_Exception('Component could not be null.', 4);

        if($pattern === null)
            throw new Hoa_Uri_Exception(
                'Pattern of %s component could not be empty.', 5, $component);

        $component = strtolower($component);

        if($value === null)
            $value = $this->{$component};

        // We consider by default that an empty value is valid.
        if(empty($value))
            return $empty;

        if(false === $out = preg_match($pattern, $value))
            throw new Hoa_Uri_Exception(
                '%s match failed', 6, ucfirst($component));

        return $out == 1;
    }

    /**
     * validateUsername
     * Check if username is valid.
     *
     * @access  public
     * @param   username  string    Username.
     * @return  bool
     */
    public function validateUsername ( $username = null ) {

        $pattern = '#^(' . $this->pattern['unreserved'] . '|' .
                   $this->pattern['pct-encoded'] . '|' .
                   '[;:&=+$,])+$#';

        return $this->_validate('username', $username, $pattern, true);
    }

    /**
     * validatePassword
     * Check if password is valid.
     *
     * @access  public
     * @param   password  string    Password.
     * @return  bool
     */
    public function validatePassword ( $password = null ) {

        $pattern = '#^(' . $this->pattern['unreserved'] . '|' .
                   $this->pattern['pct-encoded'] . '|' .
                   '[;:&=+$,])+$#';

        return $this->_validate('password', $password, $pattern, true);
    }

    /**
     * validateAuthority
     * Check if hostname is valid.
     *
     * @access  public
     * @param   host    string    Host.
     * @return  bool
     */
    public function validateAuthority ( $host = null ) {

        $pattern = '#^(' . $this->pattern['reg-name'] . ')+$#';

        return $this->_validate('authority', $host, $pattern, false);
    }

    /**
     * validatePort
     * Check if port is valid.
     *
     * @access  public
     * @param   port    int    Port.
     * @return  bool
     */
    public function validatePort ( $port = null ) {

        if($port === null)
            $port = $this->port;

        if(empty($port))
            return true;

        return ctype_digit((string)$port) && $port >= 1 && $port <= 65535;
    }

    /**
     * validatePath
     * Check if path is valid.
     *
     * @access  public
     * @param   path    string    Path.
     * @return  bool
     */
    public function validatePath ( $path = null ) {

        $pattern = '#^(' . $this->pattern['path-absolute'] . ')+$#';

        return $this->_validate('path', $path, $pattern, true);
    }

    /**
     * validateQuery
     * Check if query is valid.
     *
     * @access  public
     * @param   query   string    Query.
     * @return  bool
     */
    public function validateQuery ( $query = null ) {

        $pattern = '#^(' . $this->pattern['pchar'] . '|[/\?])+$#';

        return $this->_validate('query', $query, $pattern, true);
    }

    /**
     * validateFragment
     * Check if fragment is valid.
     *
     * @access  public
     * @param   fragment  string    Fragment.
     * @return  bool
     */
    public function validateFragment ( $fragment = null ) {

        $pattern = '#^(' . $this->pattern['pchar'] . '|[/\?])+$#';

        return $this->_validate('fragment', $fragment, $pattern, true);
    }

    /**
     * _set
     * Generic method for set*.
     *
     * @access  protected
     * @param   component  string    Component of URI.
     * @param   value      string    Value of component.
     * @return  string
     * @throw   Hoa_Uri_Exception
     */
    protected function _set ( $component = null, $value = null ) {

        if($component === null)
            throw new Hoa_Uri_Exception('Component could not be null.', 6);

        if($value === null)
            throw new Hoa_Uri_Exception('Value could not be null.', 7);

        $component = strtolower($component);
        $method    = 'validate' . ucfirst($component);

        if(!$this->$method($value))
            throw new Hoa_Uri_Exception('%s %s is not valid.',
                8, array(ucfirst($component), $value));

        $oldValue           = $this->{$component};
        $this->{$component} = $value;

        return $oldValue;
    }

    /**
     * setUsername
     * Set the username of URI.
     *
     * @access  public
     * @param   username  string    Username.
     * @return  string
     */
    public function setUsername ( $username = null ) {

        return $this->_set('username', $username);
    }

    /**
     * setPassword
     * Set the password of URI.
     *
     * @access  public
     * @param   password  string    Password.
     * @return  string
     */
    public function setPassword ( $password = null ) {

        return $this->_set('password', $password);
    }

    /**
     * setAuthority
     * Set the host of URI.
     *
     * @access  public
     * @param   host    string    Host.
     * @return  string
     */
    public function setAuthority ( $host = null ) {

        return $this->_set('authority', $host);
    }

    /**
     * setPort
     * Set the port of URI.
     *
     * @access  public
     * @param   port    string    Port.
     * @return  string
     */
    public function setPort ( $port = null ) {

        return $this->_set('port', $port);
    }

    /**
     * setPath
     * Set the path of URI.
     *
     * @access  public
     * @param   path    string    Path.
     * @return  string
     */
    public function setPath ( $path = null ) {

        return $this->_set('path', $path);
    }

    /**
     * setQuery
     * Set the query of URI.
     *
     * @access  public
     * @param   query   string    Query.
     * @return  string
     */
    public function setQuery ( $query = null ) {

        return $this->_set('query', $query);
    }

    /**
     * getUsername
     * Get username of URI.
     *
     * @access  public
     * @return  mixed
     */
    public function getUsername ( ) {

        return !empty($this->username) ? $this->username : false;
    }

    /**
     * getPassword
     * Get password of URI.
     *
     * @access  public
     * @return  mixed
     */
    public function getPassword ( ) {

        return !empty($this->password) ? $this->password : false;
    }

    /**
     * getAuthority
     * Get authority of URI.
     *
     * @access  public
     * @return  mixed
     */
    public function getAuthority ( ) {

        return !empty($this->authority) ? $this->authority : false;
    }

    /**
     * getPort
     * Get port of URI.
     *
     * @access  public
     * @return  mixed
     */
    public function getPort ( ) {

        return !empty($this->port) ? $this->port : false;
    }

    /**
     * getPath
     * Get path of URI.
     *
     * @access  public
     * @return  mixed
     */
    public function getPath ( ) {

        return !empty($this->path) ? $this->path : false;
    }

    /**
     * getQuery
     * Get query of URI.
     *
     * @access  public
     * @return  mixed
     */
    public function getQuery ( ) {

        return !empty($this->query) ? $this->query : false;
    }

    /**
     * getFragment
     * Get fragment of URI.
     *
     * @access  public
     * @return  mixed
     */
    public function getFragment ( ) {

        return !empty($this->fragment) ? $this->fragment : false;
    }
}

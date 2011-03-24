<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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

namespace Hoa\Core\Parameterizable {

/**
 * Interface \Hoa\Core\Parameterizable\Readable.
 *
 * Interface for all classes or packages which parameters are readable.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

interface Readable {

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Core\Exception
     */
    public function getParameters ( );

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function getParameter ( $key );

    /**
     * Get a formatted parameter from a class (i.e. zFormat with keywords and
     * other parameters).
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function getFormattedParameter ( $key );
}

/**
 * Interface \Hoa\Core\Parameterizable\Writable.
 *
 * Interface for all classes or packages which parameters are writable.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

interface Writable {

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in    Parameters to set.
     * @return  void
     * @throw   \Hoa\Core\Exception
     */
    public function setParameters ( Array $in );

    /**
     * Set a parameter to a class.
     *
     * @access  public
     * @param   string  $key      Key.
     * @param   mixed   $value    Value.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function setParameter ( $key, $value );
}

/**
 * Interface \Hoa\Core\Parameterizable.
 *
 * Interface for all classes or packages that are parameterizable.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

interface Parameterizable extends Readable, Writable { }

}

namespace Hoa\Core {

/**
 * Class \Hoa\Core\Parameter.
 *
 * The parameter object, contains a set of parameters. It can be shared with
 * other class with permissions (read, write, shared or combinations of these
 * ones).
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class Parameter {

    /**
     * Permission to read.
     *
     * @const int
     */
    const PERMISSION_READ    = 1;

    /**
     * Permission to write.
     *
     * @const int
     */
    const PERMISSION_WRITE   = 2;

    /**
     * Permission could be inherited.
     *
     * @const int
     */
    const PERMISSION_INHERIT = 4;

    /**
     * Permission to share.
     *
     * @const int
     */
    const PERMISSION_SHARE   = 8;

    /**
     * Collection of package's parameters.
     *
     * @var \Hoa\Core\Parameter array
     */
    private $_parameters               = array();

    /**
     * Collection of package's keywords.
     *
     * @var \Hoa\Core\Parameter array
     */
    private $_keywords                 = array();

    /**
     * Current analyzed parameters.
     *
     * @var \Hoa\Core\Parameter array
     */
    private static $_currentParameters = null;

    /**
     * Current analyzed parameter.
     *
     * @var \Hoa\Core\Parameter string
     */
    private static $_currentParameter  = null;

    /**
     * Current analyzed keywords.
     *
     * @var \Hoa\Core\Parameter array
     */
    private static $_currentKeywords   = null;

    /**
     * Parameters' owner.
     *
     * @var \Hoa\Core\Parameter string
     */
    private $_owner                    = null;

    /**
     * Owner's friends with associated permissions.
     *
     * @var \Hoa\Core\Parameter array
     */
    private $_friends                  = array();

    /**
     * Constants values.
     *
     * @var \Hoa\Core\Parameter array
     */
    private static $_constants         = array();



    /**
     * Construct a new set of parameters.
     *
     * @access  public
     * @param   \Hoa\Core\Parameterizable  $owner          Owner.
     * @param   array                      $keywords       Keywords.
     * @param   array                      $parameters     Parameters.
     * @param   string                     $ownerParent    Owner parent.
     * @return  void
     */
    public function __construct ( Parameterizable $owner,
                                  Array $keywords   = array(),
                                  Array $parameters = array(),
                                  $ownerParent      = null ) {

        if(null === $ownerParent)
            $this->_owner = get_class($owner);
        else
            if($owner instanceof $ownerParent) {

                $this->_owner = $ownerParent;
                $this->_friends[$ownerParent] =
                    self::PERMISSION_READ    |
                    self::PERMISSION_WRITE   |
                    self::PERMISSION_INHERIT |
                    self::PERMISSION_SHARE;
            }
            else
                throw new Exception(
                    'Cannot load configurations from the owner parent %s.',
                    0, $ownerParent);

        self::initializeConstants();

        if(!empty($keywords))
            $this->setKeywords($owner, $keywords);

        if(!empty($parameters))
            $this->setDefaultParameters($owner, $parameters);

        return;
    }

    /**
     * Initialize constants.
     *
     * @access  public
     * @return  void
     */
    public static function initializeConstants ( ) {

        self::$_constants = array(
            'd' => date('d'),
            'j' => date('j'),
            'N' => date('N'),
            'w' => date('w'),
            'z' => date('z'),
            'W' => date('W'),
            'm' => date('m'),
            'n' => date('n'),
            'Y' => date('Y'),
            'y' => date('y'),
            'g' => date('g'),
            'G' => date('G'),
            'h' => date('h'),
            'H' => date('H'),
            'i' => date('i'),
            's' => date('s'),
            'u' => date('u'),
            'O' => date('O'),
            'T' => date('T'),
            'U' => date('U')
        );

        return;
    }

    /**
     * Set default parameters to a class.
     *
     * @access  protected
     * @param   object  $id            Owner or friends.
     * @param   array   $parameters    Parameters to set.
     * @return  void
     * @throw   \Hoa\Core\Exception
     */
    private function setDefaultParameters ( $id, Array $parameters ) {

        $this->check($id, self::PERMISSION_WRITE);

        if($this->_owner == 'Hoa\Core\Core') {

            $class = 'HoaCoreCore';
            $path  = self::zFormat(
                $parameters['protocol.Data/Etc/Configuration'],
                $this->getKeywords($id),
                $parameters
            ) . '.Cache' . DS . 'HoaCoreCore.php';
        }
        else {

            $class = str_replace(
                '\\',
                '',
                Consistency::getClassShortestName($this->_owner)
            );
            $path  = 'hoa://Data/Etc/Configuration/.Cache/' . $class . '.php';
        }

        if(file_exists($path)) {

            $handle = require $path;

            if(!is_array($handle))
                throw new Exception(
                    'Strange though it may appear, the configuration cache ' .
                    'file %s appears to be corrupted.', 0, $path);

            if(!array_key_exists('keywords', $handle))
                throw new Exception(
                    'Need keywords in the configuration cache %s.', 1, $path);

            if(!array_key_exists('parameters', $handle))
                throw new Exception(
                    'Need parameters in the configuration cache %s.', 2, $path);

            $this->_keywords   = $handle['keywords'];
            $this->_parameters = $handle['parameters'];
        }
        else
            $this->_parameters = $parameters;

        return;
    }

    /**
     * Get default parameters from a class.
     *
     * @access  public
     * @param   object  $id    Owner or friends.
     * @return  array
     * @throw   \Hoa\Core\Exception
     */
    public function getDefaultParameters ( $id ) {

        return $this->getParameters($id);
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   object  $id    Owner or friends.
     * @param   array   $in    Parameters to set.
     * @return  void
     * @throw   \Hoa\Core\Exception
     */
    public function setParameters ( $id, Array $in ) {

        foreach($in as $key => $value)
            $this->setParameter($id, $key, $value);

        return;
    }

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @param   object  $id    Owner or friends.
     * @return  array
     * @throw   \Hoa\Core\Exception
     */
    public function getParameters ( $id ) {

        $this->check($id, self::PERMISSION_READ);

        return $this->_parameters;
    }

    /**
     * Set a parameter to a class.
     *
     * @access  public
     * @param   object  $id       Owner or friends.
     * @param   string  $key      Key.
     * @param   mixed   $value    Value.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function setParameter ( $id, $key, $value ) {

        $this->check($id, self::PERMISSION_WRITE);

        $old = null;

        if(true === array_key_exists($key, $this->_parameters))
            $old = $this->_parameters[$key];

        $this->_parameters[$key] = $value;

        return $old;
    }

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   object  $id     Owner or friends.
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function getParameter ( $id, $key ) {

        $parameters = $this->getParameters($id);

        if(array_key_exists($key, $parameters))
            return $parameters[$key];

        return null;
    }

    /**
     * Get a formatted parameter from a class (i.e. zFormat with keywords and
     * other parameters).
     *
     * @access  public
     * @param   object  $id     Owner or friends.
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function getFormattedParameter ( $id, $key ) {

        $parameter = $this->getParameter($id, $key);

        if(null === $parameter)
            return null;

        return self::zFormat(
            $parameter,
            $this->getKeywords($id),
            $this->getParameters($id)
        );
    }

    /**
     * Check a branche exists.
     *
     * @access  public
     * @param   object  $id         Owner of friends.
     * @param   string  $branche    Branche.
     * @param   array   $tree       Specify a tree if you don't want that the
     *                              method check in its own parameters.
     * @return  bool
     */
    public function brancheExists ( $id, $branche, Array $tree = array() ) {

        $handle = null;

        if(empty($tree))
            $handle = $this->getParameters($id);
        else
            $handle = $tree;

        $qBranche = preg_quote($branche);

        foreach($handle as $key => $value)
            if(0 !== preg_match('#^' . $qBranche . '(.*)?#', $key))
                return true;

        return false;
    }

    /**
     * Unlinearize a branche to an array.
     *
     * @access  public
     * @param   object  $id         Owner of friends.
     * @param   string  $branche    Branche.
     * @return  array
     */
    public function unlinearizeBranche ( $id, $branche ) {

        $parameters = $this->getParameters($id);
        $keywords   = $this->getKeywords($id);
        $out        = array();
        $qBranche   = preg_quote($branche);

        foreach($parameters as $key => $value) {

            if(0 === preg_match('#^' . $qBranche . '(.*)?#', $key, $match))
                continue;

            $handle  = array();
            $explode = preg_split(
                '#((?<!\\\)\.)#',
                $match[1],
                -1,
                PREG_SPLIT_NO_EMPTY
            );
            $end     = count($explode) - 1;
            $i       = $end;

            while($i >= 0) {

                $explode[$i] = str_replace('\\.', '.', $explode[$i]);

                if($i != $end)
                    $handle = array($explode[$i] => $handle);
                else
                    $handle = array($explode[$i] => self::zFormat(
                        $value,
                        $keywords,
                        $parameters
                    ));

                --$i;
            }

            $out = array_merge_recursive($out, $handle);
        }

        return $out;
    } 

    /**
     * Set many keywords to a class.
     *
     * @access  public
     * @param   object  $id    Owner or friends.
     * @param   array   $in    Keywords to set.
     * @return  void
     * @throw   \Hoa\Core\Exception
     */
    public function setKeywords ( $id, Array $in = array() ) {

        foreach($in as $key => $value)
            $this->setKeyword($id, $key, $value);

        return;
    }

    /**
     * Get many keywords from a class.
     *
     * @access  public
     * @param   object  $id    Owner or friends.
     * @return  array
     * @throw   \Hoa\Core\Exception
     */
    public function getKeywords ( $id ) {

        $this->check($id, self::PERMISSION_READ);

        return $this->_keywords;
    }

    /**
     * Set a keyword to a class.
     *
     * @access  public
     * @param   object  $id       Owner or friends.
     * @param   string  $key      Key.
     * @param   mixed   $value    Value.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function setKeyword ( $id, $key, $value ) {

        $this->check($id, self::PERMISSION_WRITE);

        $old = null;

        if(true === array_key_exists($key, $this->_keywords))
            $old = $this->_keywords[$key];

        $this->_keywords[$key] = $value;

        return $old;
    }

    /**
     * Get a keyword from a class.
     *
     * @access  public
     * @param   object  $id         Owner or friends.
     * @param   string  $keyword    Keyword.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function getKeyword ( $id, $keyword ) {

        $keywords = $this->getKeywords($id);

        if(true === array_key_exists($keyword, $keywords))
            return $keywords[$keyword];

        return null;
    }

    /**
     * ZFormat a string.
     * ZFormat is inspired from the famous Zsh (please, take a look at
     * http://zsh.org), and specifically from ZStyle.
     *
     * ZFormat has the following pattern:
     *     (:subject[:format]:)
     *
     * where subject could be a:
     *   * keyword, i.e. a simple string: foo;
     *   * reference to an existing parameter, i.e. a simple string prefixed by
     *     a %: %bar;
     *   * constant, i.e. a combination of chars, first is prefixed by a _: _Ymd
     *     will given the current year, followed by the current month and
     *     finally the current day.
     *
     * and where the format is a combination of chars, that apply functions on
     * the subject:
     *   * h: to get the head of a path (equivalent to dirname);
     *   * t: to get the tail of a path (equivalent to basename);
     *   * r: to get the path without extension;
     *   * e: to get the extension;
     *   * l: to get the result in lowercase;
     *   * u: to get the result in uppercase;
     *   * U: to get the result with the first letter in uppercase;
     *   * s/<foo>/<bar>/: to replace all matches <foo> by <bar> (the last / is
     *     optional, only if more options are given after);
     *   * s%<foo>%<bar>%: to replace the prefix <foo> by <bar> (the last % is
     *     also optional);
     *   * s#<foo>#<bar>#: to replace the suffix <foo> by <bar> (the last # is
     *     also optional).
     *
     * Known constants are:
     *   * d: day of the month, 2 digits with leading zeros;
     *   * j: day of the month without leading zeros;
     *   * N: ISO-8601 numeric representation of the day of the week;
     *   * w: numeric representation of the day of the week;
     *   * z: the day of the year (starting from 0);
     *   * W: ISO-8601 week number of year, weeks starting on Monday;
     *   * m: numeric representation of a month, with leading zeros;
     *   * n: numeric representation of a month, without leading zeros;
     *   * Y: a full numeric representation of a year, 4 digits;
     *   * y: a two digit representation of a year;
     *   * g: 12-hour format of an hour without leading zeros;
     *   * G: 24-hour format of an hour without leading zeros;
     *   * h: 12-hour format of an hour with leading zeros;
     *   * H: 24-hour format of an hour with leading zeros;
     *   * i: minutes with leading zeros;
     *   * s: seconds with leading zeros;
     *   * u: microseconds;
     *   * O: difference to Greenwich time (GMT) in hours;
     *   * T: timezone abbreviation;
     *   * U: seconds since the Unix Epoch (a timestamp).
     * They are very usefull for dynamic cache paths for example.
     *
     * Examples:
     *   Let keywords $k and parameters $p:
     *     $k = array(
     *         'foo'      => 'bar',
     *         'car'      => 'DeLoReAN',
     *         'power'    => 2.21,
     *         'answerTo' => 'life_universe_everything_else',
     *         'answerIs' => 42,
     *         'hello'    => 'wor.l.d'
     *     );
     *     $p = array(
     *         'plpl'        => '(:foo:U:)',
     *         'foo'         => 'ar(:%plpl:)',
     *         'favoriteCar' => 'A (:car:l:)!',
     *         'truth'       => 'To (:answerTo:ls/_/ /U:) is (:answerIs:).',
     *         'file'        => '/a/file/(:_Ymd:)/(:hello:trr:).(:power:e:)',
     *         'recursion'   => 'oof(:%foo:s#ar#az:)'
     *     );
     *   Then, after applying the zFormat, we get:
     *     * plpl:        'Bar', put the first letter in uppercase;
     *     * foo:         'arBar', call the parameter plpl;
     *     * favoriteCar: 'A delorean!', all is in lowercase;
     *     * truth:       'To Life universe everything else is 42', all is in
     *                    lowercase, then replace underscores by spaces, and
     *                    finally put the first letter in uppercase; and no
     *                    transformation for 42;
     *     * file:        '/a/file/20090505/wor.21', get date constants, then
     *                    get the tail of the path and remove extension twice,
     *                    and add the extension of power;
     *     * recursion:   'oofarBaz', get 'arbar' first, and then, replace the
     *                    suffix 'ar' by 'az'.
     *
     * @access  public
     * @param   string    $value         Parameter value.
     * @param   array     $keywords      Keywords.
     * @param   array     $parameters    Parameters.
     * @return  string
     *
     * @todo
     *   Add the cast. Maybe like this: (:subject:format[:cast]:) where cast
     * could be integer, float, array etc.
     */
    public static function zFormat ( $value,
                                     Array $keywords   = array(),
                                     Array $parameters = array() ) {

        if(!is_string($value))
            return $value;

        if(empty(self::$_constants))
            self::initializeConstants();

        self::$_currentParameters = $parameters;
        self::$_currentParameter  = $value;
        self::$_currentKeywords   = $keywords;

        $out = preg_replace_callback(
            '#\(:(.*?):\)#',
            array(__CLASS__, '_zFormat'),
            $value
        );

        return $out;
    }

    /**
     * Resolve zFormat atomically.
     *
     * @access  private
     * @param   array    $match    Match (from a regular expression).
     * @return  string
     * @throw   \Hoa\Core\Exception
     */
    private static function _zFormat ( $match ) {

        preg_match(
            '#([^:]+)(?::(.*))?#',
            $match[1],
            $submatch
        );

        if(!isset($submatch[1]))
            return '';

        $out  = null;
        $key  = $submatch[1];
        $word = substr($key, 1);

        // Call a parameter.
        if($key[0] == '%') {

            if(false === array_key_exists($word, self::$_currentParameters))
                throw new Exception(
                    'Parameter %s is not found in parameters.',
                    0, $word);

            $handle = self::$_currentParameters[$word];
            $out    = self::zFormat(
                $handle,
                self::$_currentKeywords,
                self::$_currentParameters
            );
        }
        // Call a constant (only date constants for now).
        elseif($key[0] == '_') {

            foreach(str_split($word) as $k => $v)
                if(isset(self::$_constants[$v]))
                    $out .= self::$_constants[$v];
                else
                    throw new Exception(
                        'Constant char %s is not supported in the ' .
                        'parameter rule %s.',
                        1, array($v, self::$_currentParameter));
        }
        // Call a keyword.
        else {

            if(false === array_key_exists($key, self::$_currentKeywords))
                throw new Exception(
                    'Keyword %s is not found in the parameter rule %s.', 2,
                    array($key, self::$_currentParameter));

            $out = self::$_currentKeywords[$key];
        }

        if(!isset($submatch[2]))
            return $out;

        preg_match_all(
            '#(h|t|r|e|l|u|U|s(/|%|\#)(.*?)(?<!\\\)\2(.*?)(?:(?<!\\\)\2|$))#',
            $submatch[2],
            $flags
        );

        if(empty($flags) || empty($flags[1]))
            throw new Exception(
                'Unrecognized format pattern %s in the parameter %s.',
                0, array($match[0], self::$_currentParameter));

        foreach($flags[1] as $i => $flag)
            switch($flag) {

                case 'h':
                    $out = dirname($out);
                  break;

                case 't':
                    $out = basename($out);
                  break;

                case 'r':
                    if(false !== $position = strrpos($out, '.', 1))
                        $out = substr($out, 0, $position);
                  break;

                case 'e':
                    if(false !== $position = strrpos($out, '.', 1))
                        $out = substr($out, $position + 1);
                  break;

                case 'l':
                    $out = strtolower($out);
                  break;

                case 'u':
                    $out = strtoupper($out);
                  break;

                case 'U':
                    $out = ucfirst($out);
                  break;

                default:
                    if(!isset($flags[3]) && !isset($flags[4]))
                        throw new Exception(
                            'Unrecognized format pattern in the parameter %s.',
                            0, self::$_currentParameter);

                    $l = preg_quote($flags[3][$i], '#');
                    $r = $flags[4][$i];

                    switch($flags[2][$i]) {

                        case '%':
                            $l  = '^' . $l;
                          break;

                        case '#':
                            $l .= '$';
                          break;
                    }

                    $out = preg_replace('#' . $l . '#', $r, $out);
            }

        return $out;
    }

    /**
     * Check if an object has permissions to read or write into this set of
     * parameters.
     *
     * @access  public
     * @param   object  $id             Owner or friends.
     * @param   int     $permissions    Permissions (please, see the
     *                                  self::PERMISSION_* constants).
     * @return  bool
     * @throw   \Hoa\Core\Exception
     */
    public function check ( $id, $permissions ) {

        if(!(   $id instanceof Parameterizable
             || $id instanceof Parameterizable\Readable
             || $id instanceof Parameterizable\Writable))
            throw new \Hoa\Core\Exception(
                'Class %s is not valid. ' .
                'Parameterizable classes must extend ' .
                '\Hoa\Core\Parameterizable, ' .
                '\Hoa\Core\Parameterizable\Readable or ' .
                '\Hoa\Core\Parameterizable\Writable interfaces.',
                3, $id);

        $iid = get_class($id);

        if($this->_owner == $iid)
            return true;

        if(!array_key_exists($iid, $this->_friends)) {

            $p = -1;

            foreach($this->_friends as $friend => $p)
                if(   is_subclass_of($id, $friend)
                   && $p & self::PERMISSION_INHERIT)
                    break;

            if(-1 === $p)
                throw new Exception(
                    'Class %s is not friend of %s and cannot share its parameters.',
                    4, array($iid, $this->_owner));
        }
        else
            $p = $this->_friends[$iid];

        if(0 === ($permissions & $p)) {

            if(0 !== $permissions & self::PERMISSION_READ)
                throw new Exception(
                    'Class %s does not have permission to read parameters ' .
                    'from %s.', 5, array($iid, $this->_owner));

            elseif(0 !== $permissions & self::PERMISSION_WRITE)
                throw new Exception(
                    'Class %s does not have permission to write parameters ' .
                    'from %s.', 6, array($iid, $this->_owner));

            throw new Exception(
                'Class %s does not have permission to share parameters ' .
                'from %s.', 7, array($iid, $this->_owner));
        }

        return true;
    }

    /**
     * Share this set of parameters of another class.
     * Only owner can share its set of parameters with someone else; it is more
     * simple like this… (because of changing permissions cascade effect).
     *
     * @access  public
     * @param   object  $owner          Owner or friend.
     * @param   object  $friend         Friend.
     * @param   int     $permissions    Permissions (please, see the
     *                                  self::PERMISSION_* constants).
     * @return  void
     * @throw   \Hoa\Core\Exception
     */
    public function shareWith ( $owner, $friend, $permissions ) {

        $this->check($owner, self::PERMISSION_SHARE);

        $this->_friends[get_class($friend)] = $permissions;

        return;
    }
}

}

namespace {

/**
 * Make the alias automatically (because it's not imported with the import()
 * function).
 */
class_alias('Hoa\Core\Parameterizable\Parameterizable', 'Hoa\Core\Parameterizable');

}

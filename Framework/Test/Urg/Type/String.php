<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_String
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Test_Urg_Type_Exception
 */
import('Test.Urg.Type.Exception');

/**
 * Hoa_Test_Urg_Type_Exception_Maxtry
 */
import('Test.Urg.Type.Exception.Maxtry');

/**
 * Hoa_Test_Urg_Type_Interface_Type
 */
import('Test.Urg.Type.Interface.Type');

/**
 * Hoa_Test_Urg
 */
import('Test.Urg.~');

/**
 * Hoa_Test
 */
import('Test.~');

/**
 * Class Hoa_Test_Urg_Type_String.
 *
 * Represent a string.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 *              Julien LORRAIN <julien.lorrain@gmail.com>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_String
 */

class Hoa_Test_Urg_Type_String implements Hoa_Test_Urg_Type_Interface_Type {

    /**
     * Random value.
     *
     * @var Hoa_Test_Urg_Type_String int
     */
    protected $_value    = null;

    /**
     * Category of strings.
     *
     * @var Hoa_Test_Urg_Type_String string
     */
    protected $_category = null;

    /**
     * String length.
     *
     * @var Hoa_Test_Urg_Type_String mixed
     */
    protected $_length   = null;

    /**
     * Characters to skips.
     *
     * @var Hoa_Test_Urg_Type_String array
     */
    protected $_skips    = array();



    /**
     * Constructor.
     *
     * @access  public
     * @param   string  $category    String category.
     * @param   mixed   $length      String length.
     * @param   string  $skip        Characters to skip.
     * @return  void
     */
    public function __construct ( $category, $length, $skip ) {

        $this->setCategory($category);
        $this->setLength($length);
        $this->setSkip($skip);

        return;
    }

    /**
     * A predicate.
     *
     * @access  public
     * @param   bool    $q    Q-value.
     * @return  bool
     */
    public function predicate ( $q = null ) {

        if(null === $q)
            $q = $this->getValue();

        $q          = $this->stringToArray($q);
        $dictionary = Hoa_Test::getInstance()->getParameter('test.dictionary');
        $category   = $dictionary . DS . $this->getCategory();

        if(strtolower(substr($category, -4)) != '.txt')
            $category .= DS . '*.txt';

        if(!is_dir($category))
            return false;

        $skip       = $this->getSkip();
        $length     = $this->getLength();
        $file       = null;

        if($length instanceof Hoa_Test_Urg_Type_Integer) {

            $length->randomize();
            $length = $length->getValue();
        }

        foreach(glob($category, GLOB_NOSORT) as $i => $f)
            $file  .= file_get_contents($f);

        if(mb_strlen($file, 'utf-8') == 0)
            return false;

        if(count($q) != $length)
            return false;

        foreach($q as $i => $char)
            if(false === mb_strpos($file, $char) || in_array($char, $skip))
                return false;

        return true;
    }

    /**
     * Choose a random value.
     *
     * @access  public
     * @return  void
     * @throws  Hoa_Test_Urg_Type_Exception
     * @throws  Hoa_Test_Urg_Type_Exception_Maxtry
     */
    public function randomize ( ) {

        $maxtry     = Hoa_Test::getInstance()->getParameter('test.maxtry');
        $dictionary = Hoa_Test::getInstance()->getParameter('test.dictionary');
        $category   = $dictionary . DS . $this->getCategory();

        if(strtolower(substr($category, -4)) != '.txt')
            $category .= DS . '*.txt';

        if(!is_dir($category))
            throw new Hoa_Test_Urg_Type_Exception(
                'Dictionary %s does not exist.', 0, $category);

        $skip       = $this->getSkip();
        $length     = $this->getLength();
        $file       = null;

        if($length instanceof Hoa_Test_Urg_Type_Integer) {

            $length->randomize();
            $length = $length->getValue();
        }

        foreach(glob($category, GLOB_NOSORT) as $i => $f)
            $file  .= file_get_contents($f);

        $fileLength = mb_strlen($file, 'utf-8');

        if($fileLength == 0)
            throw new Hoa_Test_Urg_Type_Exception(
                'Cannot make test because the union of %s is empty.',
                1, $category);

        do {

            $random = null;
            $i      = 0;

            while($i < $length) {

                $charPos = Hoa_Test_Urg::Ud(0, $fileLength);
                $char    = mb_substr($file, $charPos - 1, 1, 'utf-8');

                if(in_array($char, $skip))
                    continue;

                $random .= $char;
                $i++;
            }

        } while(false === $this->predicate($random) && $maxtry-- > 0);

        if($maxtry == -1)
            throw new Hoa_Test_urg_Type_Exception_Maxtry(
                'All tries failed (%d tries).',
                2, Hoa_Test::getInstance()->getParameter('test.maxtry'));

        $this->setValue($random);

        return;
    }

    /**
     * Transform a unicode string to an array.
     *
     * @access  public
     * @param   string  $string    String in unicode.
     * @return  array
     */
    public function stringToArray ( $string ) {

        $out    = array();
        $length = mb_strlen($string, 'utf-8');

        while($length > 0) {

            $out[]  = mb_substr($string, 0, 1,       'utf-8');
            $string = mb_substr($string, 1, $length, 'utf-8');
            $length = mb_strlen($string,             'utf-8');
        }

        return $out;
    }

    /**
     * Set the random value.
     *
     * @access  protected
     * @param   bool       $value    The random value.
     * @return  bool
     */
    protected function setValue ( $value ) {

        $old          = $this->_value;
        $this->_value = $value;

        return $old;
    }

    /**
     * Set string category.
     *
     * @access  protected
     * @param   string     $category    Category.
     * @return  string
     */
    protected function setCategory ( $category ) {

        $old             = $this->_category;
        $this->_category = $category;

        return $old;
    }

    /**
     * Set string length.
     *
     * @access  protected
     * @param   mixed      $length    Length.
     * @return  mixed
     */
    protected function setLength ( $length ) {

        $old           = $this->_length;
        $this->_length = $length;

        return $old;
    }

    /**
     * Set characters to skip.
     *
     * @access  protected
     * @param   string     $skip    Characters to skip.
     * @return  array
     */
    protected function setSkip ( $skip ) {

        $old         = $this->_skip;
        $this->_skip = $this->stringToArray($skip);

        return $old;
    }

    /**
     * Get the random value.
     *
     * @access  public
     * @return  bool
     */
    public function getValue ( ) {

        return $this->_value;
    }

    /**
     * Get string category.
     *
     * @access  public
     * @return  string
     */
    public function getCategory ( ) {

        return $this->_category;
    }

    /**
     * Get string length.
     *
     * @access  public
     * @return  mixed
     */
    public function getLength ( ) {

        return $this->_length;
    }

    /**
     * Get characters to skip.
     *
     * @access  public
     * @return  string
     */
    public function getSkip ( ) {

        return $this->_skip;
    }
}

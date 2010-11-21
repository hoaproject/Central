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
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_StringWith
 *
 */

/**
 * Hoa_Test_Urg_Type_Exception
 */
import('Test.Urg.Type.Exception');

/**
 * Hoa_Test_Urg_Type_Exception_Maxtry
 */
import('Test.Urg.Type.Exception.Maxtry');

/**
 * Hoa_Test_Urg
 */
import('Test.Urg.~');

/**
 * Hoa_Test
 */
import('Test.~');

/**
 * Hoa_Test_Urg_Type_SuperString
 */
import('Test.Urg.Type.SuperString');

/**
 * Class Hoa_Test_Urg_Type_StringWith.
 *
 * Represent a string.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 *              Julien LORRAIN <julien.lorrain@gmail.com>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_StringWith
 */

class Hoa_Test_Urg_Type_StringWith extends Hoa_Test_Urg_Type_SuperString {

    /**
     * Name of type.
     *
     * @var Hoa_Test_Urg_Type_Interface_Type string
     */
    protected $_name       = 'stringWith';

    /**
     * Characters that constitute the string.
     *
     * @var Hoa_Test_Urg_Type_StringWith array
     */
    protected $_characters = array();

    /**
     * String length.
     *
     * @var Hoa_Test_Urg_Type_StringWith mixed
     */
    protected $_length     = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   string  $characters    String characters.
     * @param   mixed   $length        String length.
     * @return  void
     */
    public function __construct ( $characters, $length ) {

        parent::setArguments($characters, $length);

        $this->setCharacters($characters);
        $this->setLength($length);

        return;
    }

    /**
     * A predicate.
     *
     * @access  public
     * @param   string  $q    Q-value.
     * @return  bool
     * @throw   Hoa_Test_Urg_Type_Exception
     */
    public function predicate ( $q = null ) {

        if(null === $q)
            $q = $this->getValue();

        if(false === parent::predicate($q))
            return false;

        $q          = $this->stringToArray($q);
        $length     = $this->getLength();
        $characters = $this->getCharacters();
        $charLength = count($characters);

        if($length instanceof Hoa_Test_Urg_Type_Integer)
            $length = $length->getValue();

        if($charLength == 0)
            return false;

        if(count($q) != $length)
            return false;

        foreach($q as $i => $char)
            if(!in_array($char, $characters))
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
        $length     = $this->getLength();
        $characters = $this->getCharacters();
        $charLength = count($characters);

        if($length instanceof Hoa_Test_Urg_Type_Integer) {

            $length->randomize();
            $length = $length->getValue();
        }

        if($charLength == 0)
            throw new Hoa_Test_Urg_Type_Exception(
                'Cannot make test because no character is given.',
                0, $category);

        do {

            $random = null;
            $i      = 0;

            while($i++ < $length)
                $random .= $characters[Hoa_Test_Urg::Ud(0, $charLength)];

        } while(false === $this->predicate($random) && $maxtry-- > 0);

        if($maxtry == -1)
            throw new Hoa_Test_urg_Type_Exception_Maxtry(
                'All tries failed (%d tries).',
                1, Hoa_Test::getInstance()->getParameter('test.maxtry'));

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
     * Set string characters.
     *
     * @access  protected
     * @param   string     $characters    Characters.
     * @return  string
     */
    protected function setCharacters ( $characters ) {

        $old               = $this->_characters;
        $this->_characters = $this->stringToArray($characters);

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
     * Get string characters.
     *
     * @access  public
     * @return  array
     */
    public function getCharacters ( ) {

        return $this->_characters;
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
}

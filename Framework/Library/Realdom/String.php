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
 * \Hoa\Realdom
 */
-> import('Realdom.~')

/**
 * \Hoa\Realdom\Constinteger
 */
-> import('Realdom.Constinteger');

}

namespace Hoa\Realdom {

/**
 * Class \Hoa\Realdom\String.
 *
 * Realistic domain: string.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class String extends Realdom {

    /**
     * Realistic domain name.
     *
     * @var \Hoa\Realdom string
     */
    protected $_name         = 'string';

    /**
     * Minimum code point.
     *
     * @var \Hoa\Realdom\String int
     */
    protected $_codepointMin = 0;

    /**
     * Maximum code point.
     *
     * @var \Hoa\Realdom\String int
     */
    protected $_codepointMax = 0;

    /**
     * String's length.
     *
     * @var \Hoa\Realdom\Integer object
     */
    protected $_length       = null;

    /**
     * All generated letters.
     *
     * @var \Hoa\Realdom\String array
     */
    protected $_letters      = array();



    /**
     * Construct a realistic domain.
     *
     * @access  public
     * @param   \Hoa\Realdom\Integer       $length          Length.
     * @param   \Hoa\Realdom\Constinteger  $codepointMin    Minimum code point.
     * @param   \Hoa\Realdom\Constinteger  $codepointMax    Maximum code point.
     * @return  void
     */
    public function construct ( Integer      $length       = null,
                                Constinteger $codepointMin = null,
                                Constinteger $codepointMax = null ) {

        if(null === $length)
            $length = new Constinteger(13);

        if(null === $codepointMin)
            $codepointMin = new Constinteger(0x20);

        if(null === $codepointMax)
            $codepointMax = new Constinteger(0x7E);

        $this->_length       = $length;
        $this->_codepointMin = $codepointMin->getValue();
        $this->_codepointMax = $codepointMax->getValue();

        for($i = $this->getCodepointMin(), $j = $this->getCodepointMax();
            $i <= $j;
            ++$i)
            $this->_letters[] = iconv('UCS-4LE', 'UTF-8', pack('V', $i));

        return;
    }

    /**
     * Predicate whether the sampled value belongs to the realistic domains.
     *
     * @access  public
     * @param   mixed  $q    Sampled value.
     * @return  boolean
     */
    public function predicate ( $q ) {

        if(!is_string($q))
            return false;

        $length = mb_strlen($q);

        if(false === $this->getLength()->predicate($length))
            return false;

        if(0 === $length)
            return true;

        $split  = preg_split('#(?<!^)(?!$)#u', $q);
        $out    = true;
        $handle = 0;
        $min    = $this->getCodepointMin();
        $max    = $this->getCodepointMax();

        foreach($split as $letter) {

            $handle = unpack('V', iconv('UTF-8', 'UCS-4LE', $letter));
            $out    = $out && ($min <= $handle[1]) && ($handle[1] <= $max);
        }

        return $out;
    }

    /**
     * Sample one new value.
     *
     * @access  protected
     * @param   \Hoa\Test\Sampler  $sampler    Sampler.
     * @return  mixed
     */
    protected function _sample ( \Hoa\Test\Sampler $sampler ) {

        $string  = null;
        $letters = array();
        $count   = count($this->_letters) - 1;
        $length  = $this->getLength()->sample($sampler);

        if(0 > $length)
            return false;

        for($i = 0; $i < $length; ++$i)
            $string .= $this->_letters[$sampler->getInteger(0, $count)];

        return $string;
    }

    /**
     * Get the minimum code point.
     *
     * @access  public
     * @return  int
     */
    public function getCodepointMin ( ) {

        return $this->_codepointMin;
    }

    /**
     * Get the maximum code point.
     *
     * @access  public
     * @return  int
     */
    public function getCodepointMax ( ) {

        return $this->_codepointMax;
    }

    /**
     * Get the string's length.
     *
     * @access  public
     * @return  \Hoa\Realdom\Integer
     */
    public function getLength ( ) {

        return $this->_length;
    }
}

}

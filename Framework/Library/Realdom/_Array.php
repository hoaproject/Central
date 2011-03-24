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
 * Class \Hoa\Realdom\_Array.
 *
 * Realistic domain: array.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class _Array extends Realdom {

    /**
     * Realistic domain name.
     *
     * @var \Hoa\Realdom string
     */
    protected $_name    = 'array';

    /**
     * Domains (pair => 0 (key), 1 (value) => domain disjunction).
     *
     * @var \Hoa\Realdom\_Array array
     */
    protected $_domains = null;

    /**
     * Length.
     *
     * @var \Hoa\Realdom\Constinteger object
     */
    protected $_length  = null;



    /**
     * Construct a realistic domain.
     *
     * @access  public
     * @param   array                 $domains    Domains.
     * @param   \Hoa\Realdom\Integer  $length     Length.
     * @return  void
     * @throw   \Hoa\Realdom\Exception
     */
    public function construct ( Array   $domains = array(),
                                Integer $length  = null ) {

        if(null === $length)
            $length = new Constinteger(7);

        $this->_domains = $domains;
        $this->_length  = $length;

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

        if(!is_array($q))
            return false;

        if(false === $this->getLength()->predicate(count($q)))
            return false;

        foreach($this->getDomains() as $e => $pairs) {

            $dom = false;
            $ran = false;

            foreach($q as $key => $value) {

                if(isset($pairs[0]))
                    foreach($pairs[0] as $i => $domain)
                        $dom = $dom || $domain->predicate($key);
                else
                    $dom = true;

                foreach($pairs[1] as $i => $domain)
                    $ran = $ran || $domain->predicate($value);
            }

            if(true === $dom && true === $ran)
                return true;
        }

        return false;
    }

    /**
     * Sample one new value.
     *
     * @access  protected
     * @param   \Hoa\Test\Sampler  $sampler    Sampler.
     * @return  mixed
     */
    protected function _sample ( \Hoa\Test\Sampler $sampler ) {

        $domains = $this->getDomains();
        $pair    = $domains[$sampler->getInteger(0, count($domains) - 1)];
        $length  = $this->getLength()->sample($sampler);

        if(0 > $length)
            return false;

        $domL    = count($pair[0]) - 1;
        $ranL    = count($pair[1]) - 1;
        $out     = array();

        if(!isset($pair[0]) || empty($pair[0]))
            for($i = 0; $i < $length; ++$i)
                $out[] = $pair[1][$sampler->getInteger(0, $ranL)]
                              ->sample($sampler);
        else
            for($i = 0; $i < $length; ++$i)
                $out[$pair[0][$sampler->getInteger(0, $domL)]
                    ->sample($sampler)] =
                     $pair[1][$sampler->getInteger(0, $ranL)]
                          ->sample($sampler);

        return $out;
    }

    /**
     * Get domains.
     *
     * @access  public
     * @return  array
     */
    public function getDomains ( ) {

        return $this->_domains;
    }

    /**
     * Get length.
     *
     * @access  public
     * @return  \Hoa\Realdom\Constinteger
     */
    public function getLength ( ) {

        return $this->_length;
    }
}

}

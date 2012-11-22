<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
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
-> import('Realdom.~');

}

namespace Hoa\Realdom {

/**
 * Class \Hoa\Realdom\_Array.
 *
 * Realistic domain: array.
 * Supported constraints: sorted, rsorted, ksorted, krsorted, unique.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class _Array extends Realdom {

    /**
     * Realistic domain name.
     *
     * @const string
     */
    const NAME = 'array';

    /**
     * Realistic domain defined arguments.
     *
     * @var \Hoa\Realdom array
     */
    protected $_arguments = array(
        'Constarray pairs',
        'Integer    length'
    );



    /**
     *
     */
    public function reset ( ) {

        $this->resetArguments();

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

        $count = count($q);

        if(false === $this['length']->predicate($count))
            return false;

        $pairs = $this['pairs']['pairs'];
        $out   = false;

        foreach($q as $_key => $_value) {

            $out = false;

            foreach($pairs as $pair) {

                $key   = $pair[0];
                $value = $pair[1];

                if(false === $key->predicate($_key))
                    continue;

                if(false === $value->predicate($_value))
                    continue;

                $out = true;

                break;
            }

            if(false === $out)
                return false;
        }

        if(   true === $this->is('unique')
           && $count !== count(array_unique($q, SORT_REGULAR)))
            return false;

        if(true === $this->is('sorted')) {

            $first    = true;
            $previous = array_shift($q);

            foreach($q as $value) {

                if($previous > $value)
                    return false;

                $previous = $value;
            }
        }

        if(true === $this->is('rsorted')) {

            $first    = true;
            $previous = array_shift($q);

            foreach($q as $value) {

                if($previous < $value)
                    return false;

                $previous = $value;
            }
        }

        if(true === $this->is('ksorted')) {

            $first    = true;
            reset($q);
            $previous = key($q);

            foreach($q as $key => $_) {

                if($previous > $key)
                    return false;

                $previous = $key;
            }
        }

        if(true === $this->is('krsorted')) {

            $first    = true;
            reset($q);
            $previous = key($q);

            foreach($q as $key => $_) {

                if($previous < $key)
                    return false;

                $previous = $key;
            }
        }

        return $out;
    }

    /**
     * Sample one new value.
     *
     * @access  protected
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  mixed
     */
    protected function _sample ( \Hoa\Math\Sampler $sampler ) {

        $length = $this['length']->sample($sampler);

        if(0 > $length)
            return false;

        $constraints = &$this->getConstraints();
        $out         =  array();
        $pairs       =  $this['pairs']['pairs'];
        $count       =  count($pairs) - 1;
        $unique      =  true === $this->is('unique');
        $miniMaxTry  =  $this->getMaxTry();

        if(isset($constraints['key'])) {

            $cKey = &$constraints['key'];

            foreach($cKey as $pair) {

                $value = $pair[1]->sample($sampler);

                if(true === $unique && in_array($value, $out)) {

                    if(0 >= $miniMaxTry)
                        return false;

                    --$miniMaxTry;

                    continue;
                }

                $out[$pair[0]->sample($sampler)] = $value;
            }
        }

        for($i = 0; $i < $length; ++$i) {

            $pair  = $pairs[$sampler->getInteger(0, $count)];
            $key   = $pair[0]->sample($sampler);
            $value = $pair[1]->sample($sampler);

            if(   (true === array_key_exists($key, $out))
               || (true === $unique && in_array($value, $out))) {

                if(0 >= $miniMaxTry)
                    return false;

                --$miniMaxTry;

                continue;
            }

            $out[$key] = $value;
        }

        if(true === $this->is('sorted'))
            asort($out);

        if(true === $this->is('rsorted'))
            arsort($out);

        if(true === $this->is('ksorted'))
            ksort($out);

        if(true === $this->is('krsorted'))
            krsort($out);

        return $out;
    }
}

}

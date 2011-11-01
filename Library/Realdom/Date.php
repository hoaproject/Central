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
 * \Hoa\Realdom\Conststring
 */
-> import('Realdom.Conststring')

/**
 * \Hoa\Realdom\Boundinteger
 */
-> import('Realdom.Boundinteger')

/**
 * \Hoa\Realdom\Constinteger
 */
-> import('Realdom.Constinteger');

}

namespace Hoa\Realdom {

/**
 * Class \Hoa\Realdom\String.
 *
 * Realistic domain: date.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Date extends Realdom {

    /**
     * Constants that represent defined formats.
     * As examples, you could see \DateTime constants.
     *
     * @var \Hoa\Realdom\Date array
     */
    protected static $_constants = null;

    /**
     * Realistic domain name.
     *
     * @var \Hoa\Realdom string
     */
    protected $_name             = 'date';

    /**
     * Realistic domain defined arguments.
     *
     * @var \Hoa\Realdom array
     */
    protected $_arguments        = array(
        'format',
        'timestamp'
    );



    /**
     * Construct a realistic domain.
     *
     * @access  public
     * @return  void
     */
    public function construct ( ) {

        if(null === static::$_constants) {

            $reflection         = new \ReflectionClass('DateTime');
            static::$_constants = $reflection->getConstants();
        }

        if(!isset($this['format']))
            $this['format'] = new Conststring('c');
        else {

            $constants               = &static::$_constants;
            $this['format']['value'] = preg_replace_callback(
                '#(?<!\\\)_(\w+)#',
                function ( Array $matches ) use ( &$constants ) {

                    $c = $matches[1];

                    if(!isset($constants[$c]))
                        return $matches[0];

                    return $constants[$c];
                },
                $this['format']['value']
            );
        }

        if(!isset($this['timestamp']))
            $this['timestamp'] = new Boundinteger(new Constinteger(0));

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

        $iq = strtotime($q);

        return    $this['timestamp']->predicate($iq)
               && 0 == strcasecmp(
                      $q,
                      date($this['format']->getConstantValue(), $iq)
                  );
    }

    /**
     * Sample one new value.
     *
     * @access  protected
     * @param   \Hoa\Test\Sampler  $sampler    Sampler.
     * @return  mixed
     */
    protected function _sample ( \Hoa\Test\Sampler $sampler ) {

        return date(
            $this['format']->getConstantValue(),
            $this['timestamp']->sample($sampler)
        );
    }
}

}

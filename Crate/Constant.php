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
 * \Hoa\Realdom\Exception
 */
-> import('Realdom.Exception.~')

/**
 * \Hoa\Realdom\IRealdom\Crate
 */
-> import('Realdom.I~.Crate')

/**
 * \Hoa\Realdom\IRealdom\Constant
 */
-> import('Realdom.I~.Constant');

}

namespace Hoa\Realdom\Crate {

/**
 * Class \Hoa\Realdom\Crate\Constant
 *
 * Represent a mocked constant.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class          Constant
    implements \Hoa\Realdom\IRealdom\Crate,
               \Hoa\Realdom\IRealdom\Constant {

    /**
     * Holder.
     *
     * @var \Hoa\Realdom\IRealdom\Holder object
     */
    protected $_holder                = null;

    /**
     * Praspel representation.
     *
     * @var \Closure object
     */
    protected $_praspelRepresentation = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   \Hoa\Realdom\IRealdom\Holder  $holder     Holder.
     * @param   \Closure                      $praspel    Praspel
     *                                                    representation.
     * @return  void
     */
    public function __construct ( \Hoa\Realdom\IRealdom\Holder $holder,
                                  \Closure $praspel ) {

        $this->setHolder($holder);
        $this->setPraspelRepresentation($praspel);

        return;
    }

    /**
     * Set holder.
     *
     * @access  protected
     * @param   \Hoa\Realdom\IRealdom\Holder  $holder    Holder.
     * @return  \Hoa\Realdom\IRealdom\Holder
     */
    protected function setHolder ( \Hoa\Realdom\IRealdom\Holder $holder) {

        $old           = $this->_holder;
        $this->_holder = $holder;

        return $old;
    }

    /**
     * Get holder.
     *
     * @access  public
     * @return  \Hoa\Realdom\IRealdom\Holder
     */
    public function getHolder ( ) {

        return $this->_holder;
    }

    /**
     * Get crate types.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Realdom\Exception
     */
    public function getTypes ( ) {

        $held   = $this->getHolder()->getHeld();
        $out    = array();
        $prefix = 'Hoa\Realdom\\';

        foreach($held as $realdom) {

            if($realdom instanceof \Hoa\Realdom\_Array)
                $out[] = $prefix . 'Constarray';
            elseif($realdom instanceof \Hoa\Realdom\Boolean)
                $out[] = $prefix . 'Constboolean';
            elseif($realdom instanceof \Hoa\Realdom\Float)
                $out[] = $prefix . 'Constfloat';
            elseif($realdom instanceof \Hoa\Realdom\Integer)
                $out[] = $prefix . 'Constinteger';
            elseif($realdom instanceof \Hoa\Realdom\String)
                $out[] = $prefix . 'Conststring';
            else
                throw new \Hoa\Realdom\Exception(
                    'Cannot determine the type.', 0);
        }

        return $out;
    }

    /**
     * Get constant value.
     *
     * @access  public
     * @return  mixed
     */
    public function getConstantValue ( ) {

        return $this->getHolder()->getValue();
    }

    /**
     * Set Praspel representation.
     *
     * @access  protected
     * @param   \Closure  $praspel    Praspel representation.
     * @return  \Closure
     */
    protected function setPraspelRepresentation ( \Closure $praspel ) {

        $old                          = $this->_praspelRepresentation;
        $this->_praspelRepresentation = $praspel;

        return $old;
    }

    /**
     * Get Praspel representation.
     *
     * @access  public
     * @return  \Closure
     */
    public function getPraspelRepresentation ( ) {

        return $this->_praspelRepresentation;
    }


    /**
     * Get Praspel representation of the realistic domain.
     *
     * @access  public
     * @return  string
     */
    public function toPraspel ( ) {

        $praspel = $this->getPraspelRepresentation();

        return $praspel();
    }
}

}

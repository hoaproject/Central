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

namespace Hoa\Test\Sampler {

/**
 * Class \Hoa\Test\Sampler.
 *
 * Generic sampler.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

abstract class Sampler {

    /**
     * Generate a discrete uniform distribution.
     *
     * @access  public
     * @param   int     $lower    Lower bound value.
     * @param   int     $upper    Upper bound value.
     * @return  int
     */
    public function getInteger ( $lower = null, $upper = null ) {

        if(null === $lower)
            $lower = ~PHP_INT_MAX;

        if(null === $upper)
            $upper = PHP_INT_MAX;

        if($upper !== PHP_INT_MAX)
            ++$upper;

        return $this->_getInteger($lower, $upper);
    }

    /**
     * Generate a discrete uniform distribution.
     *
     * @access  protected
     * @param   int  $lower    Lower bound value.
     * @param   int  $upper    Upper bound value.
     * @return  int
     */
    abstract protected function _getInteger ( $lower, $upper );

    /**
     * Generate a continuous uniform distribution.
     *
     * @access  public
     * @param   float   $lower    Lower bound value.
     * @param   float   $upper    Upper bound value.
     * @return  float
     */
    public function getFloat ( $lower = null, $upper = null ) {

        if(null === $lower)
            $lower = (float) ~PHP_INT_MAX;
            /*
            $lower = true === S_32\BITS
                         ? -3.4028235e38 + 1
                         : -1.7976931348623157e308 + 1;
            */

        if(null === $upper)
            $upper = (float)  PHP_INT_MAX;
            /*
            $upper = true === S_32\BITS
                         ? 3.4028235e38 - 1
                         : 1.7976931348623157e308 - 1;
            */

        return $this->_getFloat($lower, $upper);
    }

    /**
     * Generate a continuous uniform distribution.
     *
     * @access  protected
     * @param   float      $lower    Lower bound value.
     * @param   float      $upper    Upper bound value.
     * @return  float
     */
    abstract protected function _getFloat ( $lower, $upper );
}

}

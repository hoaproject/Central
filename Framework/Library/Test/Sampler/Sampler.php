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
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 */

namespace Hoa\Test\Sampler {

/**
 * Class \Hoa\Test\Sampler.
 *
 * Generic sampler.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
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

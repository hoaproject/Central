<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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

namespace Hoa\Xyl\Interpreter;

use Hoa\Consistency;

/**
 * Class \Hoa\Xyl\Interpreter.
 *
 * Abstract interpreter.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
abstract class Interpreter
{
    /**
     * Rank: abstract elements to concrete elements.
     *
     * @var array
     */
    protected $_rank         = [];

    /**
     * Resource path.
     *
     * @var string
     */
    protected $_resourcePath = null;



    /**
     * Construct interpreter.
     *
     * @param   array  $rank    Rank.
     */
    public function __construct(array $rank = [])
    {
        $this->setComponents($rank);

        return;
    }

    /**
     * Set ranks.
     *
     * @param   array  $rank    Ranks.
     * @return  void
     */
    public function setComponents(array $rank)
    {
        foreach ($rank as $element => $component) {
            $this->setComponent($element, $component);
        }

        return;
    }

    /**
     * Set rank.
     *
     * @param   array  $element      Element.
     * @param   array  $component    Classname of the component.
     * @return  void
     */
    public function setComponent($element, $component)
    {
        $this->_rank[$element] = $component;

        return;
    }

    /**
     * Get rank.
     *
     * @return  array
     */
    public function getRank()
    {
        return $this->_rank;
    }

    /**
     * Get resource path.
     *
     * @return  string
     */
    public function getResourcePath()
    {
        return $this->_resourcePath;
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity('Hoa\Xyl\Interpreter\Interpreter');

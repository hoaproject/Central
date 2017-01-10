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

namespace Hoa\Praspel\Model\Variable;

use Hoa\Consistency;
use Hoa\Praspel;
use Hoa\Realdom;

/**
 * Class \Hoa\Praspel\Model\Variable\Implicit.
 *
 * Represent an implicit variable.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Implicit extends Variable
{
    /**
     * Build a variable.
     *
     * @param   string                     $name      Name.
     * @param   bool                       $local     Local.
     * @param   \Hoa\Praspel\Model\Clause  $clause    Clause.
     * @throws  \Hoa\Praspel\Exception\Model
     */
    public function __construct(
        $name,
        $local,
        Praspel\Model\Clause $clause = null
    ) {
        if ('this' !== $name) {
            throw new Praspel\Exception\Model(
                'Variable %s is not an implicit one.',
                0,
                $name
            );
        }

        parent::__construct($name, $local, $clause);

        $this->in = realdom()->object();

        return;
    }

    /**
     * Bind the variable to a specific value.
     *
     * @param   mixed  $value    Value.
     * @return  void
     */
    public function bindTo($value)
    {
        foreach ($this->getDomains() as $domain) {
            if ($domain instanceof Realdom\Object) {
                $domain->setObject($value);
            }
        }

        return;
    }
}

if (false === Consistency::entityExists('Hoa\Realdom\Disjunction', true)) {
    throw new Praspel\Exception('Hoa\Realdom seems to not be loaded.');
}

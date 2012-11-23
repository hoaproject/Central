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
 * \Hoa\Praspel\Model\Behavior
 */
-> import('Praspel.Model.Behavior')

/**
 * \Hoa\Praspel\Model\Is
 */
-> import('Praspel.Model.Is')

/**
 * \Hoa\Realdom\Disjunction
 */
-> import('Realdom.Disjunction', true);

}

namespace Hoa\Praspel\Model {

/**
 * Class \Hoa\Praspel\Model\Specification.
 *
 * Represent a specification (contains all clauses).
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Specification extends Behavior {

    /**
     * Name.
     *
     * @const string
     */
    const NAME = '';



    /**
     * Cancel the constructor from the parent.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        return;
    }

    /**
     * Get a specific clause.
     *
     * @access  public
     * @param   string  $clause    Clause (without leading arobase).
     * @return  \Hoa\Praspel\Model\Clause
     * @throw   \Hoa\Praspel\Exception\Model
     */
    public function getClause ( $clause ) {

        if(isset($this->_clauses[$clause]))
            return $this->_clauses[$clause];

        $handle = null;

        switch($clause) {

            case 'is':
                $handle = new Is($this);
              break;

            case 'requires':
                $handle = new Requires($this);
              break;

            case 'ensures':
                $handle = new Ensures($this);
              break;

            case 'throwable':
                $handle = new Throwable($this);
              break;

            case 'invariant':
                $handle = new Invariant($this);
              break;

            case 'behavior':
                $handle = new Behavior($this);
              break;

            case 'forexample':
                $handle = new Forexample($this);
              break;

            default:
                throw new \Hoa\Praspel\Exception\Model(
                    'Clause @%s is unknown.',
                    0, $clause);
        }

        return $this->_clauses[$clause] = $handle;
    }
}

}

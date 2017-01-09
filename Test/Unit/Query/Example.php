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

namespace Hoa\Database\Test\Unit\Query;

use Hoa\Database\Query as CUT;
use Hoa\Test;

/**
 * Class \Hoa\Database\Test\Unit\Query\Example.
 *
 * Test suite of some examples.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Example extends Test\Unit\Suite implements Test\Decorrelated
{
    public function case_basic_one()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $result = (string) $q->select('a')->from('foo')
            )
            ->then
                ->string($result)
                    ->isEqualTo('SELECT a FROM foo');
    }

    public function case_reuse()
    {
        $this
            ->given(
                $q = new CUT(),
                $q->setId('x')->select('a')->from('foo')
            )
            ->when(
                $result = (string) CUT::get('x')->where('i > 3')
            )
            ->then
                ->string($result)
                    ->isEqualTo('SELECT a FROM foo WHERE i > 3');
    }

    public function case_bigger_one()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $result = (string) $q->select('a')
                                     ->select('b')
                                     ->distinct()
                                     ->from('foo')
                                     ->groupBy('a')
                                     ->having('a > 42')
                                     ->except()
                                     ->select('c')
                                     ->from('bar')
                                     ->orderBy('c')
                                     ->limit('2')
                                     ->offset('1')
            )
            ->then
                ->string($result)
                    ->isEqualTo(
                        'SELECT DISTINCT a, b FROM foo GROUP BY a ' .
                        'HAVING a > 42 EXCEPT ' .
                        'SELECT c FROM bar ORDER BY c LIMIT 2 OFFSET 1'
                    );
    }

    public function case_sub_selects()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $result = (string) $q->select('a')
                                     ->from(
                                         $q->select('a', 'b')
                                           ->from('bar')
                                     )
                                     ->leftJoin(
                                         $q->select('z')
                                           ->from('qux')
                                     )
                                     ->using('i', 'j', 'k')
                                     ->as('baz')
                                     ->limit(1)
            )
            ->then
                ->string($result)
                    ->isEqualTo(
                        'SELECT a FROM (SELECT a, b FROM bar) LEFT JOIN ' .
                        '(SELECT z FROM qux) USING (i, j, k) AS baz LIMIT 1'
                    );
    }

    public function case_sub_wheres()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $result = (string) $q->select('a')
                                     ->distinct()
                                     ->from('foo')
                                     ->where('i > 3')
                                     ->or
                                     ->where('j < 4')
                                     ->or
                                     ->where(
                                          $q->where('sub > statement')
                                            ->and
                                            ->where('x = y')
                                            ->or
                                            ->where(
                                                $q->where('grr = brouah')
                                            )
                                     )
                                     ->where('k IS NULL')
            )
            ->then
                ->string($result)
                    ->isEqualTo(
                        'SELECT DISTINCT a FROM foo WHERE i > 3 OR j < 4 OR ' .
                        '(sub > statement AND x = y OR (grr = brouah)) AND ' .
                        'k IS NULL'
                    );
    }

    public function case_default_insert()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $result = (string) $q->insert()
                                     ->into('foo')
                                     ->defaultValues()
            )
            ->then
                ->string($result)
                    ->isEqualTo(
                        'INSERT INTO foo DEFAULT VALUES'
                    );
    }

    public function case_basic_insert()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $result = (string) $q->insert()
                                     ->or
                                     ->rollback()
                                     ->into('foo')
                                     ->on('a', 'b', 'c')
                                     ->values(1, 2, 3)
            )
            ->then
                ->string($result)
                    ->isEqualTo(
                        'INSERT OR ROLLBACK INTO foo (a, b, c) VALUES (1, 2, 3)'
                    );
    }

    public function case_insert_select()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $result = (string) $q->insert()
                                     ->or
                                     ->rollback()
                                     ->into('foo')
                                     ->on('a', 'b', 'c')
                                     ->values(
                                         $q->select('a', 'b', 'c')
                                           ->from('foo')
                                           ->limit(3)
                                     )
            )
            ->then
                ->string($result)
                    ->isEqualTo(
                        'INSERT OR ROLLBACK INTO foo (a, b, c) ' .
                        'SELECT a, b, c FROM foo LIMIT 3'
                    );
    }

    public function case_basic_update()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $result = (string) $q->update()
                                     ->or
                                     ->ignore()
                                     ->table('foo')
                                     ->set('a', 13)
                                     ->set('b', 'bar')
                                     ->where('b = 0')
                                     ->or
                                     ->where(
                                         $q->where('b > 10')
                                           ->and
                                           ->where('b < 100')
                                     )
            )
            ->then
                ->string($result)
                    ->isEqualTo(
                        'UPDATE OR IGNORE foo SET a = 13, b = bar ' .
                        'WHERE b = 0 OR (b > 10 AND b < 100)'
                    );
    }

    public function case_basic_delete()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $result = (string) $q->delete()
                                     ->from('foo')
                                     ->where('b = 0')
                                     ->or
                                     ->where(
                                         $q->where('b > 10')
                                           ->and
                                           ->where('b < 100')
                                     )
            )
            ->then
                ->string($result)
                    ->isEqualTo(
                        'DELETE FROM foo WHERE b = 0 OR (b > 10 AND b < 100)'
                    );
    }
}

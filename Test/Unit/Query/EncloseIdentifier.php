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
 * Class \Hoa\Database\Test\Unit\Query\EncloseIdentifier.
 *
 * Test suite of EncloseIdentifier feature.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class EncloseIdentifier extends Test\Unit\Suite
{
    public function case_enabled()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $s = $q->select(),
                $s->enableEncloseIdentifier(),
                $result = (string) $s->select('a')
                    ->select('b')
                    ->from('foo')
            )
            ->then
                ->string($result)
                    ->isEqualTo('SELECT "a", "b" FROM "foo"');
    }

    public function case_disabled()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $s = $q->select(),
                $s->enableEncloseIdentifier(false),
                $result = (string) $s->select('a')
                    ->select('b')
                    ->from('foo')
            )
            ->then
                ->string($result)
                    ->isEqualTo('SELECT a, b FROM foo');
    }

    public function case_default()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $result = (string) $q->select('a')
                    ->select('b')
                    ->from('foo')
            )
            ->then
                ->string($result)
                    ->isEqualTo('SELECT a, b FROM foo');
    }

    public function case_set_symbol()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $s = $q->select(),
                $s->setEncloseSymbol('[', ']')
                    ->enableEncloseIdentifier(),
                $result = (string) $s->select('a')
                    ->select('b')
                    ->from('foo')
            )
            ->then
                ->string($result)
                    ->isEqualTo('SELECT [a], [b] FROM [foo]');
    }

    public function case_select()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $s = $q->select(),
                $s->enableEncloseIdentifier(),
                $result = (string) $s->select('a')
                    ->select('b')
                    ->from('foo')
                    ->where('"b" > 10') // manual enclose
                    ->groupBy('a')
                    ->having('"a" > 42') // manual enclose
                    ->orderBy('a')
                    ->limit('2')
                    ->offset('1')
            )
            ->then
                ->string($result)
                    ->isEqualTo(
                        'SELECT "a", "b" FROM "foo" ' .
                        'WHERE "b" > 10 ' .
                        'GROUP BY "a" ' .
                        'HAVING "a" > 42 ' .
                        'ORDER BY "a" LIMIT 2 OFFSET 1'
                    );
    }

    public function case_select_expr()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $s = $q->select(),
                $s->enableEncloseIdentifier(),
                $result = (string) $s->select('COUNT("a")') // manual enclose
                    ->select('b')
                    ->from('foo')
                    ->where('"b" > 10') // manual enclose
                    ->groupBy('a')
                    ->having('"a" > 42') // manual enclose
                    ->orderBy('a')
                    ->limit('2')
                    ->offset('1')
            )
            ->then
                ->string($result)
                    ->isEqualTo(
                        'SELECT COUNT("a"), "b" FROM "foo" ' .
                        'WHERE "b" > 10 ' .
                        'GROUP BY "a" ' .
                        'HAVING "a" > 42 ' .
                        'ORDER BY "a" LIMIT 2 OFFSET 1'
                    );
    }

    public function case_select_alias()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $s = $q->select(),
                $s->enableEncloseIdentifier(),
                $result = (string) $s->select('"a" AS "bar"') // manual enclose
                    ->select('b')
                    ->from('foo')
                    ->where('"b" > 10') // manual enclose
                    ->groupBy('a')
                    ->having('"a" > 42') // manual enclose
                    ->orderBy('a')
                    ->limit('2')
                    ->offset('1')
            )
            ->then
                ->string($result)
                    ->isEqualTo(
                        'SELECT "a" AS "bar", "b" FROM "foo" ' .
                        'WHERE "b" > 10 ' .
                        'GROUP BY "a" ' .
                        'HAVING "a" > 42 ' .
                        'ORDER BY "a" LIMIT 2 OFFSET 1'
                    );
    }

    public function case_select_join()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $s = $q->select(),
                $s->enableEncloseIdentifier(),
                $result = (string) $s->select('a')
                    ->select('b')
                    ->from('foo')
                    ->join('bar')
                    ->on('"b" = "baz"') // manual enclose
                    ->where('"b" > 10') // manual enclose
            )
            ->then
                ->string($result)
                    ->isEqualTo(
                        'SELECT "a", "b" FROM "foo" ' .
                        'JOIN "bar" ON "b" = "baz" ' .
                        'WHERE "b" > 10'
                    );
    }

    public function case_insert()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $i = $q->insert(),
                $i->enableEncloseIdentifier(),
                $result = (string) $i->into('foo')
                    ->on('a', 'b')
                    ->values(1, 'bar')
            )
            ->then
                ->string($result)
                    ->isEqualTo(
                        'INSERT INTO "foo" ("a", "b") VALUES (1, bar)'
                    );
    }

    public function case_update()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $u = $q->update(),
                $u->enableEncloseIdentifier(),
                $result = (string) $u->table('foo')
                    ->set('a', 13)
                    ->set('b', 'bar')
                    ->where('"b" = 0') // manual enclose
            )
            ->then
                ->string($result)
                    ->isEqualTo(
                        'UPDATE "foo" SET "a" = 13, "b" = bar WHERE "b" = 0'
                    );
    }

    public function case_delete()
    {
        $this
            ->given($q = new CUT())
            ->when(
                $d = $q->delete(),
                $d->enableEncloseIdentifier(),
                $result = (string) $d->from('foo')
                    ->where('"b" = 0') // manual enclose
            )
            ->then
                ->string($result)
                    ->isEqualTo('DELETE FROM "foo" WHERE "b" = 0');
    }
}

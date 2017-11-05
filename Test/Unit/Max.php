<?php
namespace Hoa\Heap\Test\Unit;

use Hoa\Heap\Max as SUT;
use Hoa\Test;

class Max extends Test\Unit\Suite
{
    public function case_insert_scalar()
    {
        $this
            ->given(
                $max = new SUT(),
                $keys   = '',
                $series = '',
                $four   = "ThisIsTheFourKey"
            )
            ->when(
                $max->insert('4', 79, $four),
                $three = $max->insert('3', 80),
                $six   = $max->insert('6'),
                $two   = $max->insert('2', 85),
                $one   = $max->insert('1', 90),
                $five  = $max->insert('5', 1),

                $f = function () use ($max, & $series, & $keys) {

                    foreach ($max as $key => $element) {
                        $keys .= $key;
                        $series .= $element;
                    }
                }
            )
            ->then($f())
                ->string($keys)
                    ->isIdenticalTo($one . $two . $three . $four . $five . $six)

                ->string($series)
                    ->isIdenticalTo('123456')
        ;
    }

    public function case_insert_callable()
    {
        $this
            ->given(
                $max = new SUT(),
                $series = '',
                $four   = "ThisIsTheFourKey"
            )
            ->when(
                $max->insert(xcallable(function () use (&$series) { $series .= '4'; }), 79, $four),

                $three = $max->insert(xcallable(function () use (&$series) { $series .= '3'; }), 80),
                $six   = $max->insert(xcallable(function () use (&$series) { $series .= '6'; })),
                $two   = $max->insert(xcallable(function () use (&$series) { $series .= '2'; }), 85),
                $one   = $max->insert(xcallable(function () use (&$series) { $series .= '1'; }), 90),
                $five  = $max->insert(xcallable(function () use (&$series) { $series .= '5'; }), 1),

                $f = function () use ($max) {

                    $keys = '';

                    foreach ($max as $key => $element) {
                        $keys .= $key;
                        $element();
                    }

                    return $keys;
                }
            )
            ->then
                ->variable($keys = $f())
                ->string($keys)
                    ->isIdenticalTo($one . $two . $three . $four . $five . $six)

                ->string($series)
                    ->isIdenticalTo('123456')
        ;
    }

    public function case_detach()
    {
        $this
            ->given(
                $max = new SUT()
            )
            ->when(
                $max->insert('bar', 10),
                $max->insert('baz', 5),
                $key = $max->insert('foo', 50),
                $foo = $max->detach($key)
            )
            ->then
            ->integer($max->count())
                ->isIdenticalTo(2)

            ->string($foo)
                ->isIdenticalTo('foo')
        ;
    }

    public function case_extract()
    {
        $this
            ->given(
                $max = new SUT()
            )
            ->when(
                $key = $max->insert('bar', 10),
                $max->insert('baz', 5),
                $max->insert('foo', 50),

                $max->rewind(),
                $max->next(),

                $max->extract($keyExtract, $valueExtract)
            )
            ->then
                ->string($key)
                    ->isIdenticalTo($keyExtract)

                ->string('bar')
                    ->isIdenticalTo($valueExtract)
        ;
    }

    public function case_top()
    {
        $this
            ->given(
                $max = new SUT(),
                $keys   = '',
                $series = '',
                $four = 'ThisIsTheFourKey'
            )
            ->when(
                $max->insert('4', 79, $four),
                $three = $max->insert('3', 80),
                $six   = $max->insert('6'),
                $two   = $max->insert('2', 85),
                $one   = $max->insert('1', 90),
                $five  = $max->insert('5', 1),

                $f = function () use ($max, & $series, &$keys) {

                    foreach ($max->top() as $key => $element) {
                        $series .= $element;
                        $keys .=  $key;
                    }
                }
            )
            ->then($f())
                ->integer(0)
                    ->isIdenticalTo($max->count())

                ->string($keys)
                    ->isIdenticalTo($one . $two . $three . $four . $five . $six)

                ->string($series)
                    ->isIdenticalTo('123456')
        ;
    }

    public function case_pop()
    {
        $this
            ->given(
                $max = new SUT(),
                $keys   = '',
                $series = '',
                $four = 'ThisIsTheFourKey'
            )
            ->when(
                $max->insert('4', 79, $four),
                $three = $max->insert('3', 80),
                $six   = $max->insert('6'),
                $two   = $max->insert('2', 85),
                $one   = $max->insert('1', 90),
                $five  = $max->insert('5', 1),

                $f = function () use ($max, & $series, &$keys) {

                    foreach ($max->pop() as $key => $element) {
                        $series .= $element;
                        $keys .=  $key;
                    }
                }
            )
            ->then($f())
                ->integer(0)
                    ->isIdenticalTo($max->count())

                ->string($keys)
                    ->isIdenticalTo($six . $five . $four . $three . $two . $one)

                ->string($series)
                    ->isIdenticalTo('654321')
        ;
    }

    public function case_end()
    {
        $this
            ->given(
                $max = new SUT()
            )
            ->when(
                $key = $max->insert('bar', 10),
                $max->insert('baz', 5),
                $max->insert('foo', 50),

                $max->rewind(),
                $max->end(),

                $value = $max->current()
            )
            ->then
                ->string('baz')
                    ->isIdenticalTo($value)
        ;
    }

    public function case_priority()
    {
        $this
            ->given(
                $max = new SUT()
            )
            ->when(
                $max->insert('bar', 10),
                $max->insert('baz', 5),
                $max->insert('foo', 50),

                $max->rewind(),
                $max->next()
            )
            ->then
                ->integer(10)
                    ->isIdenticalTo($max->priority())
        ;
    }

    public function case_current()
    {
        $this
            ->given(
                $max = new SUT()
            )
            ->when(
                $max->insert('bar', 10),
                $max->insert('baz', 5),
                $max->insert('foo', 50),

                $max->rewind(),
                $max->next(),

                $value = $max->current()
            )
            ->then
                ->string('bar')
                    ->isIdenticalTo($value)
        ;
    }

    public function case_key()
    {
        $this
            ->given(
                $max = new SUT()
            )
            ->when(
                $max->insert('bar', 10),
                $max->insert('baz', 5),
                $key = $max->insert('foo', 50),

                $max->rewind(),

                $_key = $max->key()
            )
            ->then
                ->string($key)
                    ->isIdenticalTo($_key)
        ;
    }

    public function case_next()
    {
        $this
            ->given(
                $max = new SUT()
            )
            ->when(
                $max->insert('bar', 10),
                $max->insert('baz', 5),
                $max->insert('foo', 50),

                $max->rewind(),
                $max->next(),
                $max->next(),

                $value = $max->current()
            )
            ->then
                ->string('baz')
                    ->isIdenticalTo($value)
        ;
    }

    public function case_rewind()
    {
        $this
            ->given(
                $max = new SUT()
            )
            ->when(
                $max->insert('bar', 10),
                $max->insert('baz', 5),
                $max->insert('foo', 50),

                $max->rewind(),
                $max->next(),
                $max->next(),
                $max->rewind(),

                $value = $max->current()
            )
            ->then
            ->string('foo')
            ->isIdenticalTo($value)
        ;
    }

    public function case_valid()
    {
        $this
            ->given(
                $max = new SUT()
            )
            ->when(
                $max->insert('bar', 10),
                $max->insert('baz', 5),

                $max->rewind(),
                $max->next()
            )
            ->then
                ->boolean(true)
                    ->isIdenticalTo($max->valid())
            ->when(
                $max->next()
            )
            ->then
                ->boolean(false)
                    ->isIdenticalTo($max->valid())
        ;
    }

    public function case_count()
    {
        $this
            ->given(
                $max = new SUT()
            )
            ->when(
                $max->insert('bar', 10),
                $max->insert('baz', 5),
                $max->insert('foo', 50),
                $max->insert('bar', 10),
                $max->insert('baz', 5),
                $max->insert('foo', 50),
                $max->insert('bar', 10),
                $max->insert('baz', 5),
                $max->insert('foo', 50)
            )
            ->then
                ->integer(9)
                    ->isIdenticalTo($max->count())
        ;
    }
}

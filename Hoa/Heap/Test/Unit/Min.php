<?php
namespace Hoa\Heap\Test\Unit;

use Hoa\Heap\Min as SUT;
use Hoa\Test;

class Min extends Test\Unit\Suite
{
    public function case_insert_scalar()
    {
        $this
            ->given(
                $min = new SUT(),
                $keys   = '',
                $series = '',
                $four   = "ThisIsTheFourKey"
            )
            ->when(
                $min->insert('4', 79, $four),
                $three = $min->insert('3', 80),
                $six   = $min->insert('6'),
                $two   = $min->insert('2', 85),
                $one   = $min->insert('1', 90),
                $five  = $min->insert('5', 1),

                $f = function () use ($min, & $series, & $keys) {

                    foreach ($min as $key => $element) {
                        $keys .= $key;
                        $series .= $element;
                    }
                }
            )
            ->then($f())
            ->string($keys)
            ->isIdenticalTo($six . $five . $four . $three . $two . $one)

            ->string($series)
            ->isIdenticalTo('654321')
        ;
    }

    public function case_insert_callable()
    {
        $this
            ->given(
                $min = new SUT(),
                $series = '',
                $four   = "ThisIsTheFourKey"
            )
            ->when(
                $min->insert(xcallable(function () use (&$series) { $series .= '4'; }), 79, $four),

                $three = $min->insert(xcallable(function () use (&$series) { $series .= '3'; }), 80),
                $six   = $min->insert(xcallable(function () use (&$series) { $series .= '6'; })),
                $two   = $min->insert(xcallable(function () use (&$series) { $series .= '2'; }), 85),
                $one   = $min->insert(xcallable(function () use (&$series) { $series .= '1'; }), 90),
                $five  = $min->insert(xcallable(function () use (&$series) { $series .= '5'; }), 1),

                $f = function () use ($min) {

                    $keys = '';

                    foreach ($min as $key => $element) {
                        $keys .= $key;
                        $element();
                    }

                    return $keys;
                }
            )
            ->then
            ->variable($keys = $f())
            ->string($keys)
            ->isIdenticalTo($six . $five . $four . $three . $two . $one)

            ->string($series)
            ->isIdenticalTo('654321')
        ;
    }

    public function case_detach()
    {
        $this
            ->given(
                $min = new SUT()
            )
            ->when(
                $min->insert('bar', 10),
                $min->insert('baz', 5),
                $key = $min->insert('foo', 2),
                $foo = $min->detach($key)
            )
            ->then
            ->integer($min->count())
            ->isIdenticalTo(2)

            ->string($foo)
            ->isIdenticalTo('foo')
        ;
    }

    public function case_extract()
    {
        $this
            ->given(
                $min = new SUT()
            )
            ->when(
                $key = $min->insert('bar', 10),
                $min->insert('baz', 5),
                $min->insert('foo', 13),
                $min->insert('bil', 42),

                $min->rewind(),
                $min->next(),

                $min->extract($keyExtract, $valueExtract)
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
                $min = new SUT(),
                $keys   = '',
                $series = '',
                $four = 'ThisIsTheFourKey'
            )
            ->when(
                $min->insert('4', 79, $four),
                $three = $min->insert('3', 80),
                $six   = $min->insert('6'),
                $two   = $min->insert('2', 85),
                $one   = $min->insert('1', 90),
                $five  = $min->insert('5', 1),

                $f = function () use ($min, & $series, &$keys) {

                    foreach ($min->top() as $key => $element) {
                        $series .= $element;
                        $keys .=  $key;
                    }
                }
            )
            ->then($f())
            ->integer(0)
            ->isIdenticalTo($min->count())

            ->string($keys)
            ->isIdenticalTo($six . $five . $four . $three . $two . $one)

            ->string($series)
            ->isIdenticalTo('654321')
        ;
    }

    public function case_pop()
    {
        $this
            ->given(
                $min = new SUT(),
                $keys   = '',
                $series = '',
                $four = 'ThisIsTheFourKey'
            )
            ->when(
                $min->insert('4', 79, $four),
                $three = $min->insert('3', 80),
                $six   = $min->insert('6'),
                $two   = $min->insert('2', 85),
                $one   = $min->insert('1', 90),
                $five  = $min->insert('5', 1),

                $f = function () use ($min, & $series, &$keys) {

                    foreach ($min->pop() as $key => $element) {
                        $series .= $element;
                        $keys .=  $key;
                    }
                }
            )
            ->then($f())
            ->integer(0)
            ->isIdenticalTo($min->count())

            ->string($keys)
            ->isIdenticalTo($one . $two . $three . $four . $five . $six)

            ->string($series)
            ->isIdenticalTo('123456')
        ;
    }

    public function case_end()
    {
        $this
            ->given(
                $min = new SUT()
            )
            ->when(
                $key = $min->insert('bar', 10),
                $min->insert('baz', 5),
                $min->insert('foo', 2),

                $min->rewind(),
                $min->end(),

                $value = $min->current()
            )
            ->then
            ->string('bar')
            ->isIdenticalTo($value)
        ;
    }

    public function case_priority()
    {
        $this
            ->given(
                $min = new SUT()
            )
            ->when(
                $min->insert('bar', 10),
                $min->insert('baz', 5),
                $min->insert('foo', 2),

                $min->rewind(),
                $min->next()
            )
            ->then
            ->integer(5)
            ->isIdenticalTo($min->priority())
        ;
    }

    public function case_current()
    {
        $this
            ->given(
                $min = new SUT()
            )
            ->when(
                $min->insert('bar', 10),
                $min->insert('rab', 2),
                $min->insert('baz', 5),
                $min->insert('foo', 50),

                $min->rewind(),
                $min->next(),

                $value = $min->current()
            )
            ->then
            ->string('baz')
            ->isIdenticalTo($value)
        ;
    }

    public function case_key()
    {
        $this
            ->given(
                $min = new SUT()
            )
            ->when(
                $min->insert('bar', 10),
                $min->insert('baz', 5),
                $min->insert('rab', 3),
                $key = $min->insert('foo', 2),

                $min->rewind(),

                $_key = $min->key()
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
                $min = new SUT()
            )
            ->when(
                $min->insert('bar', 10),
                $min->insert('baz', 5),
                $min->insert('foo', 50),
                $min->insert('rab', 2),

                $min->rewind(),
                $min->next(),
                $min->next(),

                $value = $min->current()
            )
            ->then
            ->string('bar')
            ->isIdenticalTo($value)
        ;
    }

    public function case_rewind()
    {
        $this
            ->given(
                $min = new SUT()
            )
            ->when(
                $min->insert('bar', 10),
                $min->insert('baz', 5),
                $min->insert('foo', 50),
                $min->insert('rab', 2),

                $min->rewind(),
                $min->next(),
                $min->next(),
                $min->rewind(),

                $value = $min->current()
            )
            ->then
            ->string('rab')
            ->isIdenticalTo($value)
        ;
    }

    public function case_valid()
    {
        $this
            ->given(
                $min = new SUT()
            )
            ->when(
                $min->insert('bar', 10),
                $min->insert('baz', 5),

                $min->rewind(),
                $min->next()
            )
            ->then
            ->boolean(true)
            ->isIdenticalTo($min->valid())
            ->when(
                $min->next()
            )
            ->then
            ->boolean(false)
            ->isIdenticalTo($min->valid())
        ;
    }

    public function case_count()
    {
        $this
            ->given(
                $min = new SUT()
            )
            ->when(
                $min->insert('bar', 10),
                $min->insert('baz', 5),
                $min->insert('foo', 50),
                $min->insert('bar', 10),
                $min->insert('baz', 5),
                $min->insert('foo', 50),
                $min->insert('bar', 10),
                $min->insert('baz', 5),
                $min->insert('foo', 50)
            )
            ->then
            ->integer(9)
            ->isIdenticalTo($min->count())
        ;
    }
}

<?php

use Streams as S;
use Streams\Stream;

class StreamTest extends PHPUnit_Framework_TestCase
{
    private $array = array(1, 2, 3, 4);

    public function testConstructor()
    {
        $stream = new Stream($this->array);

        $this->assertTrue($stream instanceof Stream);

        $this->assertEquals($this->array, $stream->toArray());
    }

    public function testMap()
    {
        $stream = new Stream($this->array);

        $callback = function ($item) {
            return $item * 2;
        };

        $result = $stream->map($callback);
        $this->assertEquals(array(2, 4, 6, 8), $result->toArray());

        $secondResult = $result
            ->map($callback)
            ->map($callback);
        $this->assertEquals(array(8, 16, 24, 32), $secondResult->toArray());
    }

    public function testFilter()
    {
        $stream = new Stream($this->array);

        $predicate = function ($item) {
            return !($item % 2);
        };

        $result = $stream->filter($predicate);
        $this->assertEquals(array(2, 4), $result->toArray());

        $stream = new Stream($this->array);

        $predicateEven = function ($item) {
            return !($item % 2);
        };

        $predicateEqualsFour = function ($item) {
            return $item == 4;
        };

        $secondResult = $stream->filter($predicateEven)->filter($predicateEqualsFour);
        $this->assertEquals(array(4), $secondResult->toArray());
    }

    public function testNestedMapAndFilterCallsWorksAsExpected()
    {
        $stream = new Stream($this->array);

        $result = $stream
            ->map(function ($item) {
                return $item * 3;
            })->filter(function ($item) {
                return $item % 6 == 0;
            });

        $this->assertEquals($result->toArray(), array(6, 12));
    }

    public function testAllMatch()
    {
        $stream = new Stream(array(2, 4, 6, 8));

        $result = $stream->allMatch(function ($item) {
            return !($item % 2);
        });

        $this->assertEquals(true, $result);
    }

    public function testAnyMatch()
    {
        $stream = new Stream($this->array);

        $result = $stream->anyMatch(function ($item) {
            return !($item % 3);
        });

        $this->assertTrue($result);
    }

    public function testConcatStreams()
    {
        $streamA = new Stream($this->array);
        $streamB = new Stream($this->array);
        $newStream = Stream::concat($streamA, $streamB);

        $this->assertInstanceOf('Streams\Base\BaseStream', $newStream);

        $this->assertEquals($newStream->toArray(), $streamA->toArray() + $streamB->toArray());
    }

    public function testCount()
    {
        $stream = new Stream($this->array);

        $this->assertEquals(4, $stream->count());
    }

    public function testDistinct()
    {
        $stream = new Stream([1, 2, 3, 4, 1, 2, 3, 4, 1, 2, 3, 4, 1, 2, 3, 4]);
        $newStream = $stream->distinct();
        $this->assertInstanceOf('Streams\Stream', $newStream);

        $this->assertEquals($newStream->toArray(), [1, 2, 3, 4]);

        $stream = new Stream([4, 5, 6, 7, 4, 5, 6, 7]);
        $newStream = $stream->distinct();
        $this->assertInstanceOf('Streams\Stream', $newStream);

        $this->assertEquals($newStream->toArray(), [4, 5, 6, 7]);
    }

    public function testMapToFloat()
    {
        $stream = new Stream([1, 2, 3, 4, 1, 2, 3, 4, 1, 2, 3, 4, 1, 2, 3, 4]);
        $newStream = $stream->mapToFloat(function ($item) {
            return $item;
        });
        $this->assertInstanceOf('Streams\FloatStream', $newStream);
    }

    public function testMapToInt()
    {
        $stream = new Stream([1, 2, 3, 4, 1, 2, 3, 4, 1, 2, 3, 4, 1, 2, 3, 4]);
        $newStream = $stream->mapToInt(function ($item) {
            return $item;
        });
        $this->assertInstanceOf('Streams\IntStream', $newStream);
    }

    public function testReduce()
    {
        $stream = new Stream([1, 2, 3, 4]);
        $sum = $stream->reduce(0, function ($item, $next) {
            return $item + $next;
        });
        $this->assertEquals(10, $sum);

        $mult = $stream->reduce(1, function ($item, $next) {
            return $item * $next;
        });
        $this->assertEquals(24, $mult);
    }

    public function testLetsDoCoolThingsSuchAsMapReduce()
    {
        $arrayOfPhrases = array(
            'first second third',
            'first second',
            'fourth second fourth',
            'first second second',
            'third second third',
        );

        $phrasesStream = new Stream($arrayOfPhrases);

        $computedArray = $phrasesStream
            ->map(function ($line) {
                return array_count_values(explode(' ', $line));
            })
            ->reduce(array(), function ($acc, $next) {
                foreach ($next as $word => $count) {
                    if (isset($acc[$word]))
                        $acc[$word] += $count;
                    else
                        $acc[$word] = $count;
                }

                return $acc;
            });

        $this->assertEquals(3, $computedArray['first']);
        $this->assertEquals(6, $computedArray['second']);
        $this->assertEquals(3, $computedArray['third']);
        $this->assertEquals(2, $computedArray['fourth']);
    }
}

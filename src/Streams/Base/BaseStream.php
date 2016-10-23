<?php

namespace Streams\Base;

use Streams as S;
use Streams\FloatStream;
use Streams\Interfaces;
use Streams\IntStream;
use Streams\Stream;

abstract class BaseStream implements Interfaces\Streamer
{
    /* @var array */
    private $elements = [];

    public function __construct(...$elements)
    {
        $this->elements = $elements;
    }

    /**
     * Creates a lazily concatenated stream whose elements are all the elements
     * of the first stream followed by all the elements of the second stream.
     *
     * @param Stream $a
     * @param Stream $b
     *
     * @return Stream
     */
    public static function concat(Stream $a, Stream $b)
    {
        $items = $a->toArray() + $b->toArray();
        return new Stream($items);
    }

    /**
     * Call a function on all elements.
     *
     * @param $callback
     *
     * @return $this
     */
    public function each($callback)
    {
        array_map($callback, $this->toArray());
        return $this;
    }

    /**
     * Runs a function on all elements and filters the stream based on the results.
     *
     * @param $callback
     *
     * @return BaseStream
     */
    public function filter($callback)
    {
        $elements = array_values(array_filter($this->toArray(), $callback));
        return $this->newStream($elements);
    }

    public function newStream(...$elements)
    {
        $class = get_called_class();
        return new $class($elements);
    }

    /**
     * Returns the number of elements in the stream.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->elements;
    }

    /**
     * @return array
     *
     * @deprecated Use {@link toArray()} instead.
     */
    public function getElements()
    {
        return $this->toArray();
    }

    /**
     * setElements
     *
     * @param array $elements
     *
     * @return void
     */
    public function setElements(...$elements)
    {
        $this->elements = $elements;
    }

    public function addElements(...$elements)
    {
        (new Stream($elements))->each(function($e) {
            $this->addElement($e);
        });
    }

    private function addElement($e)
    {
        $this->elements[] = $e;
    }

    /**
     * Returns a new stream consisting of the distinct elements of the stream
     *
     * @return BaseStream
     */
    public function distinct()
    {
        $elements = array_unique($this->toArray());
        return $this->newStream($elements);
    }

    /**
     * Returns whether all the elements in the stream match the given predicate
     *
     * @param $callback
     *
     * @return bool
     */
    public function allMatch($callback)
    {
        $prevLength = count($this->toArray());
        return count(array_filter($this->toArray(), $callback)) == $prevLength;
    }

    /**
     * Returns whether any of the elements in the stream match the given
     * predicate
     *
     * @param $callback
     *
     * @return bool
     */
    public function anyMatch($callback)
    {
        return !empty(count(array_filter($this->toArray(), $callback)));
    }

    /**
     * Map the elements in the stream to floats.
     *
     * @param $callback
     *
     * @return FloatStream
     */
    public function mapToFloat($callback)
    {
        $items = $this->map($callback)->toArray();
        return new FloatStream($items);
    }

    /**
     * Map a function to each element in a collection
     *
     * @param $callback
     *
     * @return BaseStream
     */
    public function map($callback)
    {
        $elements = array_map($callback, $this->toArray());
        $this->setElements(...$elements);
        return $this;
    }

    /**
     * Map the elements in the stream to integers.
     *
     * @param $callback
     *
     * @return IntStream
     */
    public function mapToInt($callback)
    {
        $this->map($callback);
        return new IntStream($this->toArray());
    }

    /**
     * Creates an empty Stream
     *
     * @return $this
     */
    public function emptyStream()
    {
        return $this->newStream([]);
    }

    /**
     * reduce
     *
     * This operation performs a reduction on the elements of the stream. The
     * first param is used as the initial element for the
     *
     * @param mixed $initial
     * @param       $callback
     *
     * @return mixed
     */
    public function reduce($initial, $callback)
    {
        return array_reduce($this->toArray(), $callback, $initial);
    }
}

<?php

namespace Streams\Base;

use Streams\Interfaces;

class NumericStream extends BaseStream implements Interfaces\NumericStreamer
{
    public static function of()
    {
        $args = func_get_args();
        $class = get_called_class();
        return new $class(array_values($args));
    }

    public function max()
    {
        return max($this->toArray());
    }

    public function min()
    {
        return min($this->toArray());
    }
}

<?php

namespace Streams;

use Streams\Base;

class IntStream extends Base\NumericStream
{
    public function __construct( array $elements )
    {
        $this->setElements(array_map(function($item) {
            return (int) $item;
        }, $elements));
        return $this;
    }
}

<?php

namespace Streams;

use Streams\Base;

class FloatStream extends Base\NumericStream
{
    public function __construct( array $elements )
    {
        $this->setElements(array_map(function($item) {
            return (float) $item;
        }, $elements));
        return $this;
    }
}

<?php

namespace Streams;

use Streams\Base;

class FloatStream extends Base\NumericStream
{
    public function __construct(array $elements)
    {
        parent::__construct($elements);
        $this->map(function($e) { return (float) $e; });
    }
}

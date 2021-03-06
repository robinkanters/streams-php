<?php

namespace Streams;

use Streams\Base;

class IntStream extends Base\NumericStream
{
    public function __construct(array $elements)
    {
        parent::__construct($elements);
        $this->map(function($e) { return (int) $e; });
    }
}

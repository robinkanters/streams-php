<?php

namespace Streams\Interfaces;

use Streams\Base\BaseStream;

interface NumericStreamer extends Streamer
{
    /* @return BaseStream */
    public static function of();

    /* @return mixed */
    public function max();

    /* @return mixed */
    public function min();
}

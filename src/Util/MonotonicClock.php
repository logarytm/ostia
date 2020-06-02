<?php

declare(strict_types=1);

namespace App\Util;

final class MonotonicClock
{
    private function __construct()
    {
        // Do not instantiate me.
    }

    public static function nanoseconds(): float
    {
        return (float)hrtime(/* get_as_number */ true);
    }
}

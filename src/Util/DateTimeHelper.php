<?php

declare(strict_types=1);

namespace App\Util;

use DateInterval;

final class DateTimeHelper
{
    public static function intervalFromTotalSeconds(float $totalSeconds)
    {
        return new DateInterval();
    }
}

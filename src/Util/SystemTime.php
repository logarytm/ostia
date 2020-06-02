<?php

declare(strict_types=1);

namespace App\Util;

use DateTimeImmutable;
use DateTimeZone;

final class SystemTime
{
    private function __construct()
    {
        // Do not instantiate me.
    }

    public static function utcNow(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}

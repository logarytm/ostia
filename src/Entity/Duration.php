<?php

declare(strict_types=1);

namespace App\Entity;

final class Duration
{
    private int $seconds;

    private function __construct(int $seconds)
    {
        $this->seconds = $seconds;
    }

    public static function fromSeconds(int $seconds): Duration
    {
        return new Duration($seconds);
    }

    public function getTotalSeconds(): int
    {
        return $this->seconds;
    }

    public function __toString(): string
    {
        $minutes = intdiv($this->seconds, 60);
        $seconds = $this->seconds % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}

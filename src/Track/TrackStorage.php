<?php

declare(strict_types=1);

namespace App\Track;

use Symfony\Component\Filesystem\Filesystem;

class TrackStorage
{
    public function __construct(Filesystem $filesystem)
    {
    }
}

<?php

declare(strict_types=1);

namespace App\Track;

use Ramsey\Uuid\UuidInterface;

class TrackFileStorage
{
    private string $temporaryDir;

    public function __construct(string $temporaryDir)
    {
        $this->temporaryDir = $temporaryDir;
    }

    public function getTemporaryFilePath(UuidInterface $uuid): string
    {
        return sprintf('%s/%s', $this->temporaryDir, $uuid->toString());
    }
}

<?php

declare(strict_types=1);

namespace App\Track;

use App\Entity\Track;
use App\Entity\TrackFile;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Filesystem\Filesystem;

class TrackAudioStorage
{
    private string $temporaryDir;
    private string $persistentDir;

    public function __construct(string $temporaryDir, string $persistentDir)
    {
        $this->temporaryDir = $temporaryDir;
        $this->persistentDir = $persistentDir;
    }

    public function getTemporaryFilePath(UuidInterface $uuid): string
    {
        return sprintf('%s/%s', $this->temporaryDir, $uuid->toString());
    }

    public function saveToPersistentStorage(Track $track, TrackFile $trackFile): void
    {
        (new Filesystem())->rename(
            $this->getTemporaryFilePath($trackFile->getUuid()),
            $this->getAudioFilePath($track)
        );
    }

    private function getAudioFilePath(Track $track): string
    {
        return sprintf('%s/%s', $this->persistentDir, $track->getId()->toString());
    }
}

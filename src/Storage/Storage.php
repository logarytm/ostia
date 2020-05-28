<?php

declare(strict_types=1);

namespace App\Storage;

use App\Entity\Track;
use App\Entity\TrackUpload;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Filesystem\Filesystem;

class Storage
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

    public function saveToPersistentStorage(Track $track, TrackUpload $trackFile): void
    {
        (new Filesystem())->rename(
            $this->getTemporaryFilePath($trackFile->getId()),
            $this->getAudioFilePath($track)
        );
    }

    private function getAudioFilePath(Track $track): string
    {
        return sprintf('%s/%s', $this->persistentDir, $track->getId()->toString());
    }
}

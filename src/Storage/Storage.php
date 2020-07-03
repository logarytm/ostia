<?php

declare(strict_types=1);

namespace App\Storage;

use App\Entity\Track;
use App\Exception\UploadNotFoundException;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Filesystem\Filesystem;

class Storage
{
    private string $temporaryDir;
    private string $persistentDir;
    private string $persistentUrl;

    public function __construct(string $temporaryDir, string $persistentDir, string $persistentUrl)
    {
        $this->temporaryDir = $temporaryDir;
        $this->persistentDir = $persistentDir;
        $this->persistentUrl = $persistentUrl;
    }

    public function getTemporaryFilePath(UuidInterface $uuid): string
    {
        return sprintf('%s/%s', $this->temporaryDir, $uuid->toString());
    }

    public function saveToPersistentStorage(Track $track, Track $trackUpload): void
    {
        if ($trackUpload->getStatus() !== Track::STATUS_UPLOADED) {
            throw new UploadNotFoundException();
        }

        (new Filesystem())->rename(
            $this->getTemporaryFilePath($trackUpload->getId()),
            $this->getAudioFilePath($track)
        );
    }

    public function getUrl(string $catalog, UuidInterface $id): string
    {
        return sprintf('%s/%s', $this->persistentUrl, $id->toString());
    }

    private function getAudioFilePath(Track $track): string
    {
        return sprintf('%s/%s', $this->persistentDir, $track->getId()->toString());
    }
}

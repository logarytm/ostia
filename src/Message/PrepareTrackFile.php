<?php

declare(strict_types=1);

namespace App\Message;

use Ramsey\Uuid\UuidInterface;

final class PrepareTrackFile
{
    private string $originalFilename;
    private UuidInterface $trackFileUuid;

    public function __construct(UuidInterface $trackFileId, string $originalFilename)
    {
        $this->trackFileUuid = $trackFileId;
        $this->originalFilename = $originalFilename;
    }

    public function getOriginalFilename(): string
    {
        return $this->originalFilename;
    }

    public function getTrackFileId(): UuidInterface
    {
        return $this->trackFileUuid;
    }
}

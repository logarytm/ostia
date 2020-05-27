<?php

declare(strict_types=1);

namespace App\Message;

use Ramsey\Uuid\UuidInterface;

final class PrepareTrackFile
{
    private UuidInterface $trackFileUuid;

    public function __construct(UuidInterface $trackFileId)
    {
        $this->trackFileUuid = $trackFileId;
    }

    public function getTrackFileId(): UuidInterface
    {
        return $this->trackFileUuid;
    }
}

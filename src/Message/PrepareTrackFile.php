<?php

declare(strict_types=1);

namespace App\Message;

use Ramsey\Uuid\UuidInterface;

final class PrepareTrackFile
{
    private UuidInterface $trackFileUuid;

    public function __construct(UuidInterface $trackFileUuid)
    {
        $this->trackFileUuid = $trackFileUuid;
    }

    public function getTrackFileUuid(): UuidInterface
    {
        return $this->trackFileUuid;
    }
}

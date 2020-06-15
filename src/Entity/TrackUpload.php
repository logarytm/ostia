<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

class TrackUpload extends TrackBase implements TrackInterface
{
    public function __construct(
        UuidInterface $id,
        User $user,
        string $title,
        int $ordering,
        DateTimeImmutable $dateCreated
    ) {
        parent::__construct(
            $id,
            $user,
            $title,
            null,
            $ordering,
            $dateCreated,
            self::STATUS_UPLOADED,
        );
    }

    public function setDuration(Duration $duration): void
    {
        $this->duration = $duration;
    }

    public function isDurationComputed(): bool
    {
        return $this->duration !== null;
    }

    public function isUploadReady(): bool
    {
        return $this->isDurationComputed();
    }
}

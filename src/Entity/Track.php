<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

class Track extends TrackBase implements TrackInterface
{
    public function __construct(
        UuidInterface $id,
        User $user,
        string $title,
        ?Duration $duration,
        int $ordering,
        DateTimeImmutable $dateCreated
    ) {
        parent::__construct(
            $id,
            $user,
            $title,
            $duration,
            $ordering,
            $dateCreated,
            self::STATUS_REVIEWED,
        );
    }
}

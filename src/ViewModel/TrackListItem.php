<?php

declare(strict_types=1);

namespace App\ViewModel;

use App\Entity\Duration;
use App\Entity\Track;
use Ramsey\Uuid\UuidInterface;

final class TrackListItem
{
    private UuidInterface $id;
    private string $title;
    private Duration $duration;

    public function __construct(
        UuidInterface $id,
        string $title,
        Duration $duration
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->duration = $duration;
    }

    public static function fromEntity(Track $track): self
    {
        return new TrackListItem(
            $track->getId(),
            $track->getTitle(),
            $track->getDuration()
        );
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDuration(): Duration
    {
        return $this->duration;
    }
}

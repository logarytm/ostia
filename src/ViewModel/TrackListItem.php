<?php

declare(strict_types=1);

namespace App\ViewModel;

use App\Entity\Duration;
use App\Entity\Track;

final class TrackListItem
{
    private int $id;
    private string $title;
    private Duration $duration;

    public function __construct(
        int $id,
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
            $track->getId() ?? 0,
            $track->getTitle(),
            $track->getDuration()
        );
    }

    public function getId(): int
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

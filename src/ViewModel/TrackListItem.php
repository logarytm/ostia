<?php

declare(strict_types=1);

namespace App\ViewModel;

use App\Entity\Track;
use DateInterval;

final class TrackListItem
{
    private int $id;
    private string $title;
    private DateInterval $duration;

    public function __construct(
        int $id,
        string $title,
        DateInterval $duration
    )
    {
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

    public function getDuration(): DateInterval
    {
        return $this->duration;
    }
}

<?php

declare(strict_types=1);

namespace App\Metadata;

use App\Entity\Duration;

class Metadata
{
    private Duration $duration;
    private ?string $genre;
    private string $title;

    public function __construct(
        string $title,
        Duration $duration,
        ?string $genre
    ) {
        $this->title = $title;
        $this->duration = $duration;
        $this->genre = $genre;
    }

    public function getDuration(): Duration
    {
        return $this->duration;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}

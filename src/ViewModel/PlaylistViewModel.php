<?php

declare(strict_types=1);

namespace App\ViewModel;

class PlaylistViewModel
{
    private TrackList $tracks;
    private string $name;

    public function __construct(string $name, TrackList $tracks)
    {
        $this->name = $name;
        $this->tracks = $tracks;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTracks(): TrackList
    {
        return $this->tracks;
    }
}
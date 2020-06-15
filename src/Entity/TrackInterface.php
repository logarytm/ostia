<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

interface TrackInterface
{
    public const STATUS_UPLOADED = 0;
    public const STATUS_REVIEWED = 1;

    public function getId(): UuidInterface;

    public function getTitle(): ?string;

    public function rename(string $newTitle): void;

    public function getDuration(): Duration;

    public function getDateCreated(): DateTimeImmutable;

    public function getMetadata(): TrackMetadata;

    public function addToPlaylist(Playlist $playlist): self;

    public function removeFromPlaylist(Playlist $playlist): self;

    public function getStatus(): int;

    public function setStatus(int $status): void;

    public function getUser(): ?User;
}

<?php

declare(strict_types=1);

namespace App\ViewModel;

use App\Entity\TrackBase;
use Ramsey\Uuid\UuidInterface;

final class TrackToReview
{
    public const PENDING = 'pending';

    private UuidInterface $id;
    private string $filename;
    private ?string $title;
    private ?array $artists;
    private ?array $albumArtists;
    private ?string $album;
    private ?int $trackNo;
    private string $status;

    public function __construct(
        UuidInterface $id,
        string $filename,
        ?string $title,
        ?array $artists,
        ?array $albumArtists,
        ?string $album,
        ?int $trackNo
    ) {
        $this->id = $id;
        $this->filename = $filename;
        $this->title = $title;
        $this->artists = $artists;
        $this->albumArtists = $albumArtists;
        $this->album = $album;
        $this->trackNo = $trackNo;
        $this->status = self::PENDING;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getArtists(): ?array
    {
        return $this->artists;
    }

    public function getAlbumArtists(): ?array
    {
        return $this->albumArtists;
    }

    public function getAlbum(): ?string
    {
        return $this->album;
    }

    public function getTrackNo(): ?int
    {
        return $this->trackNo;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public static function fromUpload(TrackBase $upload): TrackToReview
    {
        return new self(
            $upload->getId(),
            $upload->getTitle(),
            $upload->getMetadata()->title,
            $upload->getMetadata()->artists,
            $upload->getMetadata()->albumArtists,
            $upload->getMetadata()->album,
            $upload->getMetadata()->trackNo
        );
    }
}

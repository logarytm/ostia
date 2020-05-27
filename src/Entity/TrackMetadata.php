<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class TrackMetadata
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    public ?string $title;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    public ?array $artists;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    public ?array $albumArtists;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    public ?string $album;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public ?int $trackNo;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    public ?string $genre;
}

<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TrackRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=TrackRepository::class)
 */
class Track
{
    public const STATUS_UPLOADED = 0;
    public const STATUS_REVIEWED = 1;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * @ORM\Column(type="duration", nullable=true)
     */
    private ?Duration $duration;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $dateCreated;

    /**
     * @ORM\Column(type="integer")
     */
    private int $ordering;

    /**
     * @ORM\ManyToMany(targetEntity=Playlist::class, inversedBy="tracks")
     */
    private Collection $playlists;

    /**
     * @ORM\Column(type="integer")
     */
    private int $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tracks")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    /**
     * @ORM\Embedded(class=TrackMetadata::class)
     */
    private TrackMetadata $metadata;

    public function __construct(
        UuidInterface $id,
        User $user,
        string $title,
        ?Duration $duration,
        int $ordering,
        DateTimeImmutable $dateCreated,
        int $status
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->title = $title;
        $this->duration = $duration;
        $this->dateCreated = $dateCreated;
        $this->ordering = $ordering;
        $this->playlists = new ArrayCollection();
        $this->metadata = new TrackMetadata();
        $this->status = $status;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function rename(string $newTitle): void
    {
        $this->title = $newTitle;
    }

    public function getDuration(): Duration
    {
        return $this->duration;
    }

    public function setDuration(Duration $duration): void
    {
        $this->duration = $duration;
    }

    public function isDurationComputed(): bool
    {
        return $this->duration !== null;
    }

    public function getDateCreated(): DateTimeImmutable
    {
        return $this->dateCreated;
    }

    public function getMetadata(): TrackMetadata
    {
        return $this->metadata;
    }

    public function addToPlaylist(Playlist $playlist): self
    {
        if (!$this->playlists->contains($playlist)) {
            $this->playlists[] = $playlist;
        }

        return $this;
    }

    public function removeFromPlaylist(Playlist $playlist): self
    {
        if ($this->playlists->contains($playlist)) {
            $this->playlists->removeElement($playlist);
        }

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function isUploadReady(): bool
    {
        return $this->isDurationComputed() && $this->status === self::STATUS_UPLOADED;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}

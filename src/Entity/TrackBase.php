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
 * @ORM\Table(name="track")
 */
class TrackBase implements TrackInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    protected UuidInterface $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $title;

    /**
     * @ORM\Column(type="duration", nullable=true)
     */
    protected ?Duration $duration;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    protected DateTimeImmutable $dateCreated;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $ordering;

    /**
     * @ORM\ManyToMany(targetEntity=Playlist::class, inversedBy="tracks")
     * @ORM\JoinTable(name="track_playlist", joinColumns={@ORM\JoinColumn(name="track_id", referencedColumnName="id")})
     */
    protected Collection $playlists;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tracks")
     * @ORM\JoinColumn(nullable=false)
     */
    protected User $user;

    /**
     * @ORM\Embedded(class=TrackMetadata::class)
     */
    protected TrackMetadata $metadata;

    /**
     * This class is not designed to be instantinated directly, please see class-level description
     */
    protected function __construct(
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

    public function getUser(): ?User
    {
        return $this->user;
    }
}

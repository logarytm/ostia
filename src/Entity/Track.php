<?php

namespace App\Entity;

use App\Repository\TrackRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=TrackRepository::class)
 */
class Track
{
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
     * @ORM\Column(type="duration")
     */
    private Duration $duration;

    /**
     * @ORM\ManyToMany(targetEntity=Playlist::class, inversedBy="tracks")
     */
    private Collection $playlists;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tracks")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    /**
     * @ORM\Embedded(class=TrackMetadata::class)
     */
    private TrackMetadata $metadata;

    public function __construct(UuidInterface $id, User $user, string $title, Duration $duration)
    {
        $this->id = $id;
        $this->user = $user;
        $this->title = $title;
        $this->duration = $duration;
        $this->playlists = new ArrayCollection();
        $this->metadata = new TrackMetadata();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDuration(): Duration
    {
        return $this->duration;
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

    public function getUser(): User
    {
        return $this->user;
    }
}

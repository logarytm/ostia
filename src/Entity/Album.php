<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=AlbumRepository::class)
 * @ORM\Table(name="albums")
 */
class Album
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToMany(targetEntity=Artist::class, inversedBy="albums", cascade={"persist"})
     */
    private Collection $artists;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $title;

    /**
     * @ORM\OneToMany(targetEntity=Track::class, mappedBy="album", orphanRemoval=true)
     */
    private Collection $tracks;

    public function __construct(UuidInterface $id, ?string $title)
    {
        $this->id = $id;
        $this->title = $title;
        $this->artists = new ArrayCollection();
        $this->tracks = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /** @return Collection|Artist[] */
    public function getArtists(): Collection
    {
        return $this->tracks;
    }

    public function addArtist(Artist $artist): self
    {
        if (!$this->artists->contains($artist)) {
            $this->artists[] = $artist;
        }

        return $this;
    }

    public function removeArtist(Artist $artist): self
    {
        if ($this->artists->contains($artist)) {
            $this->artists->removeElement($artist);
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /** @return Collection|Track[] */
    public function getTracks(): Collection
    {
        return $this->tracks;
    }

    public function addTrack(Track $track): self
    {
        if (!$this->tracks->contains($track)) {
            $this->tracks[] = $track;
            $track->setAlbum($this);
        }

        return $this;
    }

    public function removeTrack(Track $track): self
    {
        if ($this->tracks->contains($track)) {
            $this->tracks->removeElement($track);
            // set the owning side to null (unless already changed)
            if ($track->getAlbum() === $this) {
                $track->setAlbum(null);
            }
        }

        return $this;
    }
}

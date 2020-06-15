<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=ArtistRepository::class)
 */
class Artist
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToMany(targetEntity=Album::class, mappedBy="artists")
     */
    private Collection $albums;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $name;

    public function __construct(UuidInterface $id, ?string $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->albums = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /** @return Collection|Album[] */
    public function getAlbums(): Collection
    {
        return $this->albums;
    }

    public function addAlbum(Album $album): self
    {
        if (!$this->albums->contains($album)) {
            $this->albums[] = $album;
        }

        return $this;
    }

    public function removeAlbum(Album $album): self
    {
        if ($this->albums->contains($album)) {
            $this->albums->removeElement($album);
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}

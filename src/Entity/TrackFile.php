<?php

namespace App\Entity;

use App\Repository\TrackFileRepository;
use DateInterval;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Form\Exception\LogicException;

/**
 * @ORM\Entity(repositoryClass=TrackFileRepository::class)
 */
class TrackFile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="uuid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="duration", nullable=true)
     */
    private ?Duration $duration;

    /**
     * @ORM\Embedded(class=TrackMetadata::class)
     */
    private $metadata;

    public function __construct()
    {
        $this->metadata = new TrackMetadata();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function setUuid(UuidInterface $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMetadata(): TrackMetadata
    {
        return $this->metadata;
    }

    public function getDuration(): Duration
    {
        if (!$this->isDurationComputed()) {
            throw new LogicException('Check isDurationComputed() before calling getDuration().');
        }

        return $this->duration;
    }

    public function isDurationComputed(): bool
    {
        return $this->duration !== null;
    }

    public function isReady(): bool
    {
        return $this->isDurationComputed();
    }

    public function setDuration(Duration $duration): void
    {
        $this->duration = $duration;
    }
}

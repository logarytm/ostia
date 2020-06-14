<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Form\Exception\LogicException;

/**
 * @ORM\Entity(repositoryClass=TrackFileRepository::class)
 */
class TrackUpload
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="duration", nullable=true)
     */
    private ?Duration $duration;

    /**
     * @ORM\Embedded(class=TrackMetadata::class)
     */
    private TrackMetadata $metadata;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $dateCreated;

    /**
     * @ORM\Column(type="float")
     */
    private float $ordering;

    public function __construct(UuidInterface $id, string $name, DateTimeImmutable $dateCreated, float $ordering)
    {
        $this->id = $id;
        $this->name = $name;
        $this->metadata = new TrackMetadata();
        $this->dateCreated = $dateCreated;
        $this->ordering = $ordering;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->name;
    }

    public function getMetadata(): TrackMetadata
    {
        return $this->metadata;
    }

    public function getOrdering(): float
    {
        return $this->ordering;
    }

    public function getDuration(): Duration
    {
        if (!$this->isDurationComputed()) {
            throw new LogicException('Check isDurationComputed() before calling getDuration().');
        }

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

    public function isReady(): bool
    {
        return $this->isDurationComputed();
    }
}

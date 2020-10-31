<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Genre;
use App\Entity\Track;
use App\Metadata\Metadata;
use App\Repository\GenreRepository;
use App\Repository\TrackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class TrackManager
{
    private EntityManagerInterface $entityManager;
    private GenreRepository $genreRepository;
    private TrackRepository $trackRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        GenreRepository $genreRepository,
        TrackRepository $trackRepository
    ) {
        $this->entityManager = $entityManager;
        $this->genreRepository = $genreRepository;
        $this->trackRepository = $trackRepository;
    }

    public function updateMetadata(Track $track, Metadata $metadata): Track
    {
        $track->setDuration($metadata->getDuration());
        $track->rename($metadata->getTitle());

        if ($metadata->getGenre() !== null) {
            $genreName = $metadata->getGenre();
            $track->setGenre($this->genreRepository->getByName($genreName) ?? new Genre(Uuid::uuid4(), $genreName));
        }

        $this->entityManager->persist($track);
        $this->entityManager->flush();

        return $track;
    }
}

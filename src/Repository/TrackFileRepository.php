<?php

namespace App\Repository;

use App\Entity\TrackFile;
use App\Exception\TrackFileNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

/**
 * @method TrackFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrackFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrackFile[]    findAll()
 * @method TrackFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrackFile::class);
    }

    public function add(TrackFile $trackFile)
    {
        $this->_em->persist($trackFile);
        $this->_em->flush();
    }

    /**
     * @param UuidInterface[] $uuids
     * @return TrackFile[]
     */
    public function getByUuids(array $uuids): array
    {
        return $this->_em
            ->createQuery('SELECT tf FROM App\Entity\TrackFile tf WHERE tf.uuid IN (:uuids)')
            ->setParameter(':uuids', $uuids)
            ->getResult();
    }

    public function remove(TrackFile $trackFile): void
    {
        $this->_em->remove($trackFile);
        $this->_em->flush();
    }

    public function getByUuid(UuidInterface $uuid): TrackFile
    {
        $trackFile = $this->findOneBy(['uuid' => $uuid]);
        if ($trackFile === null) {
            throw new TrackFileNotFoundException(sprintf(
                'Track with UUID %s not found.',
                $uuid
            ));
        }

        return $trackFile;
    }
}

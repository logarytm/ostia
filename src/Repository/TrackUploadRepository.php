<?php

namespace App\Repository;

use App\Entity\TrackUpload;
use App\Entity\User;
use App\Exception\UploadNotFoundException;
use App\ViewModel\TrackToReview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

/**
 * @method TrackUpload|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrackUpload|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrackUpload[]    findAll()
 * @method TrackUpload[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackUploadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrackUpload::class);
    }

    public function add(TrackUpload $trackFile)
    {
        $this->_em->persist($trackFile);
        $this->_em->flush();
    }

    /**
     * @return TrackUpload[]
     */
    public function getByIds(UuidInterface ...$ids): array
    {
        return $this->_em
            ->createQuery('SELECT tu FROM App\Entity\TrackUpload tu WHERE tu.id IN (:ids) ORDER BY tu.ordering')
            ->setParameter(':ids', $ids)
            ->getResult();
    }

    public function remove(TrackUpload $trackFile): void
    {
        $this->_em->remove($trackFile);
        $this->_em->flush();
    }

    public function getById(UuidInterface $id): TrackUpload
    {
        $trackFile = $this->findOneBy(['id' => $id]);
        if ($trackFile === null) {
            throw new UploadNotFoundException(sprintf(
                'Upload with UUID %s not found.',
                $id
            ));
        }

        return $trackFile;
    }

    public function getEndPosition(User $user): int
    {
        return (int)$this->_em
            ->getConnection()
            ->fetchColumn('SELECT MAX(t.position) + 1 FROM track t WHERE t.user_id = :user_id', [
                ':user_id' => $user->getId(),
            ]);
    }

    /** @return TrackToReview[] */
    public function getTracksToReview(UuidInterface ...$ids): array
    {
        $uploads = $this->getByIds(...$ids);

        return array_map([TrackToReview::class, 'fromUpload'], $uploads);
    }
}

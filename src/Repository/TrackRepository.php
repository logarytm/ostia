<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TrackBase;
use App\Entity\TrackInterface;
use App\Entity\TrackUpload;
use App\Entity\User;
use App\Exception\UploadNotFoundException;
use App\ViewModel\TrackList;
use App\ViewModel\TrackListItem;
use App\ViewModel\TrackToReview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

/**
 * @method TrackInterface|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrackInterface|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrackInterface[]    findAll()
 * @method TrackInterface[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrackBase::class);
    }

    /** @return TrackList */
    public function all(User $user, int $status = TrackInterface::STATUS_REVIEWED): TrackList
    {
        $tracks = $this->findBy(['user' => $user, 'status' => $status], ['ordering' => 'ASC', 'dateCreated' => 'ASC']);
        $items = array_map([TrackListItem::class, 'fromEntity'], $tracks);

        return new TrackList(...$items);
    }

    public function add(TrackInterface $track): void
    {
        $this->_em->persist($track);
        $this->_em->flush();
    }

    public function remove(TrackInterface $trackFile): void
    {
        $this->_em->remove($trackFile);
        $this->_em->flush();
    }

    /** @return TrackInterface[] */
    public function getByIds(UuidInterface ...$ids): array
    {
        return $this->_em
            ->createQuery('SELECT t FROM App\Entity\TrackBase t WHERE t.id IN (:ids) ORDER BY t.ordering')
            ->setParameter(':ids', $ids)
            ->getResult();
    }

    public function getTrackUploadById(UuidInterface $id): TrackUpload
    {
        /** @var TrackUpload $trackFile */
        $trackFile = $this->findOneBy(['id' => $id, 'status' => TrackInterface::STATUS_UPLOADED]);
        if ($trackFile === null) {
            throw new UploadNotFoundException(sprintf(
                'Upload with UUID %s not found.',
                $id
            ));
        }

        return $trackFile;
    }

    public function getEndPosition(User $user, int $status): int
    {
        return (int)$this->_em
            ->getConnection()
            ->fetchColumn('SELECT MAX(t.ordering) + 1 FROM track t WHERE t.user_id = :user_id AND status = :status', [
                ':user_id' => $user->getId(),
                'status' => $status,
            ]);
    }

    /** @return TrackToReview[] */
    public function getTracksToReview(UuidInterface ...$ids): array
    {
        $uploads = $this->getByIds(...$ids);

        return array_map([TrackToReview::class, 'fromUpload'], $uploads);
    }
}

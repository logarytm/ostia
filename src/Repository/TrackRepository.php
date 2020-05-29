<?php

namespace App\Repository;

use App\Entity\Track;
use App\Entity\TrackUpload;
use App\Entity\User;
use App\ViewModel\TrackList;
use App\ViewModel\TrackListItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Track|null find($id, $lockMode = null, $lockVersion = null)
 * @method Track|null findOneBy(array $criteria, array $orderBy = null)
 * @method Track[]    findAll()
 * @method Track[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Track::class);
    }

    public function add(Track $track): void
    {
        $this->_em->persist($track);
        $this->_em->flush();
    }

    /**
     * @return TrackList
     */
    public function all(User $user): TrackList
    {
        $tracks = $this->findBy(['user' => $user], ['dateCreated' => 'ASC']);
        $items = array_map([TrackListItem::class, 'fromEntity'], $tracks);

        return new TrackList(...$items);
    }
}

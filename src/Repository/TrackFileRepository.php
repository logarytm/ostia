<?php

namespace App\Repository;

use App\Entity\TrackFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    // /**
    //  * @return TrackFile[] Returns an array of TrackFile objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TrackFile
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

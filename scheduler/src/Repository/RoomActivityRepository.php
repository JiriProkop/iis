<?php

namespace App\Repository;

use App\Entity\RoomActivity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RoomActivity>
 *
 * @method RoomActivity|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoomActivity|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoomActivity[]    findAll()
 * @method RoomActivity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoomActivity::class);
    }

//    /**
//     * @return RoomActivity[] Returns an array of RoomActivity objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RoomActivity
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

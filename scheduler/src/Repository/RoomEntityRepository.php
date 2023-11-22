<?php

namespace App\Repository;

use App\Entity\RoomEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RoomEntity>
 *
 * @method RoomEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoomEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoomEntity[]    findAll()
 * @method RoomEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoomEntity::class);
    }

//    /**
//     * @return RoomEntity[] Returns an array of RoomEntity objects
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

//    public function findOneBySomeField($value): ?RoomEntity
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

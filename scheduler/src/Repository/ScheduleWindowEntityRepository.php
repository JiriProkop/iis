<?php

namespace App\Repository;

use App\Entity\ScheduleWindowEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ScheduleWindowEntity>
 *
 * @method ScheduleWindowEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScheduleWindowEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScheduleWindowEntity[]    findAll()
 * @method ScheduleWindowEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScheduleWindowEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScheduleWindowEntity::class);
    }

//    /**
//     * @return ScheduleWindowEntity[] Returns an array of ScheduleWindowEntity objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ScheduleWindowEntity
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

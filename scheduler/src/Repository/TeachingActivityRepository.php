<?php

namespace App\Repository;

use App\Entity\TeachingActivity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TeachingActivity>
 *
 * @method TeachingActivity|null find($id, $lockMode = null, $lockVersion = null)
 * @method TeachingActivity|null findOneBy(array $criteria, array $orderBy = null)
 * @method TeachingActivity[]    findAll()
 * @method TeachingActivity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeachingActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeachingActivity::class);
    }

//    /**
//     * @return TeachingActivity[] Returns an array of TeachingActivity objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TeachingActivity
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\SchedulerWindow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SchedulerWindow>
 *
 * @method SchedulerWindow|null find($id, $lockMode = null, $lockVersion = null)
 * @method SchedulerWindow|null findOneBy(array $criteria, array $orderBy = null)
 * @method SchedulerWindow[]    findAll()
 * @method SchedulerWindow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchedulerWindowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchedulerWindow::class);
    }

//    /**
//     * @return SchedulerWindow[] Returns an array of SchedulerWindow objects
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

//    public function findOneBySomeField($value): ?SchedulerWindow
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

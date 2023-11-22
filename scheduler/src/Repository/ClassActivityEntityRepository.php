<?php

namespace App\Repository;

use App\Entity\ClassActivityEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ClassActivityEntity>
 *
 * @method ClassActivityEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClassActivityEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClassActivityEntity[]    findAll()
 * @method ClassActivityEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassActivityEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClassActivityEntity::class);
    }

//    /**
//     * @return ClassActivityEntity[] Returns an array of ClassActivityEntity objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ClassActivityEntity
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

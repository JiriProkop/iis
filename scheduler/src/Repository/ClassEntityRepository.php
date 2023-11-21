<?php

namespace App\Repository;

use App\Entity\ClassEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ClassEntity>
 *
 * @method ClassEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClassEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClassEntity[]    findAll()
 * @method ClassEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClassEntity::class);
    }

//    /**
//     * @return ClassEntity[] Returns an array of ClassEntity objects
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

//    public function findOneBySomeField($value): ?ClassEntity
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

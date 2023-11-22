<?php

namespace App\Repository;

use App\Entity\PersonalActivityEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonalActivityEntity>
 *
 * @method PersonalActivityEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonalActivityEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonalActivityEntity[]    findAll()
 * @method PersonalActivityEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonalActivityEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalActivityEntity::class);
    }

//    /**
//     * @return PersonalActivityEntity[] Returns an array of PersonalActivityEntity objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PersonalActivityEntity
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\UserInClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserInClass>
 *
 * @method UserInClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserInClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserInClass[]    findAll()
 * @method UserInClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserInClassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInClass::class);
    }

    //    /**
//     * @return UserInClass[] Returns an array of UserInClass objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    //    public function findOneBySomeField($value): ?UserInClass
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

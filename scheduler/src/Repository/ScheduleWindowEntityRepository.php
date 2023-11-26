<?php

namespace App\Repository;


use App\Entity\ScheduleWindowEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

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

    public function getWindowsForRoomBetween(\DateTimeInterface $start_date, \DateTimeInterface $end_date, string $room): array
    {
        $start_date_between = 'w.Start <= :start_date AND :start_date < w.End';
        $window_start_between = ':start_date <= w.Start AND w.Start < :end_date';
        $qb = $this->createQueryBuilder('w')
            ->leftJoin('w.ClassActivity', 'ca', Expr\Join::WITH, 'w.ClassActivity != null') //TODO potrebuji ziskat okna dle jejich mistnosti tak to potrebuji propojit
            ->leftJoin('ca.Rooms', 'ca_r', Expr\Join::WITH, 'w.ClassActivity != null') // && ca.Rooms != []')
            ->leftJoin('w.PersonalActivity', 'pa', Expr\Join::WITH, 'w.PersonalActivity != null') //TODO potrebuji ziskat okna dle jejich mistnosti tak to potrebuji propojit
            ->leftJoin('pa.Rooms', 'pa_r', Expr\Join::WITH, 'w.PersonalActivity != null')
            ->andWhere($start_date_between . 'OR' . $window_start_between)
            ->setParameters(new ArrayCollection([new Parameter('start_date', $start_date),
                new Parameter('end_date', $end_date)]))
            ->andWhere('(ca_r IS NOT NULL AND ca_r.Name = :room) OR (pa_r IS NOT NULL AND pa_r.Name = :room)')
            ->setParameter('room', $room);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function getWindowsOfActivitiesBetween(\DateTimeInterface $start_date, \DateTimeInterface $end_date): array
    {
        $start_date_between = 'w.Start <= :start_date && :start_date < w.End';
        $window_start_between = ':start_date <= w.Start && w.Start < :end_date';
        $qb = $this->createQueryBuilder('w')
            ->andWhere($start_date_between . '||' . $window_start_between)
            ->setParameters(new ArrayCollection([new Parameter('start_date', $start_date),
                                                 new Parameter('end_date', $end_date)]));

        $query = $qb->getQuery();

        return $query->getResult();
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

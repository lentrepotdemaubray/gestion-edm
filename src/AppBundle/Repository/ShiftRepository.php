<?php

namespace AppBundle\Repository;

/**
 * ShiftRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ShiftRepository extends \Doctrine\ORM\EntityRepository
{

    public function findFutures()
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->where('s.start > :now')
            ->setParameter('now', new \Datetime('now'))
            ->orderBy('s.start', 'ASC');

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function findFuturesWithJob($job,\DateTime $max = null)
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->join('s.job', "job")
            ->where('s.start > :now')
            ->andwhere('job.id = :jid')
            ->setParameter('now', new \Datetime('now'))
            ->setParameter('jid', $job->getId());

        if ($max){
            $qb
                ->andWhere('s.end < :max')
                ->setParameter('max', $max);
        }

        $qb->orderBy('s.start', 'ASC');

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function findFrom(\DateTime $from,\DateTime $max = null)
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->select('s, f')
            ->leftJoin('s.formation', 'f')
            ->where('s.start > :from')
            ->setParameter('from', $from);
        if ($max){
            $qb
                ->andWhere('s.end < :max')
                ->setParameter('max', $max);
        }

        $qb->orderBy('s.start', 'ASC');

        return $qb
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $user
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findFirst($user)
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->join('s.shifter', "ben")
            ->where('ben.user = :user')
            ->setParameter('user', $user)
            ->andWhere('s.isDismissed = 0')
            ->orderBy('s.start', 'ASC')
            ->setMaxResults(1);

        return $qb
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findReservedBefore(\DateTime $max)
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->where('s.start < :max')
            ->andWhere('s.lastShifter is not null')
            ->setParameter('max', $max);

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function findReservedAt(\DateTime $date)
    {
        $qb = $this->createQueryBuilder('s');

        $datePlusOne = clone $date;
        $datePlusOne->modify('+1 day');

        $qb
            ->where('s.start >= :date')
            ->andwhere('s.start < :datePlusOne')
            ->andWhere('s.lastShifter is not null')
            ->setParameter('date', $date)
            ->setParameter('datePlusOne',$datePlusOne );

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function findFirstShiftWithUserNotInitialized()
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->join('s.shifter', "ben")
            ->join('ben.membership', "m")
            ->where('m.firstShiftDate is NULL')
            ->addOrderBy('m.id', 'ASC')
            ->addOrderBy('s.start', 'ASC');

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function findFreeAt(\DateTime $date, $job)
    {
        $qb = $this->createQueryBuilder('s');

        $datePlusOne = clone $date;
        $datePlusOne->modify('+1 day');

        $qb
            ->where('s.shifter is null')
            ->orWhere('s.isDismissed = 1')
            ->andwhere('s.job = :job')
            ->andwhere('s.start >= :date')
            ->andwhere('s.start < :datePlusOne')
            ->setParameter('job', $job)
            ->setParameter('date', $date)
            ->setParameter('datePlusOne',$datePlusOne );

        return $qb
            ->getQuery()
            ->getResult();
    }
}

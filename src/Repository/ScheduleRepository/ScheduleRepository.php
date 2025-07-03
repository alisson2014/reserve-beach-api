<?php

declare(strict_types=1);

namespace App\Repository\ScheduleRepository;

use App\Entity\Schedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ScheduleRepository extends ServiceEntityRepository implements IScheduleRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Schedule::class);
    }

    public function add(Schedule $schedule, bool $flush = false): Schedule
    {
        $this->getEntityManager()->persist($schedule);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
        return $schedule;
    }

    public function getByDate(): array
    {
        $query = $this->createQueryBuilder('s')
            ->select(
                'u.name AS userName',
                'c.name AS courtName',
                'cs.startTime',
                'cs.endTime',
                'c.schedulingFee'
            )
            ->innerJoin('s.courtSchedule', 'cs')
            ->innerJoin('cs.court', 'c')
            ->innerJoin('s.user', 'u')
            // ->where('s.scheduledAt = CURRENT_DATE()')
            ->getQuery();

        $results = $query->getArrayResult();

        return array_map(function ($item) {
            $item['schedulingFee'] = (float) $item['schedulingFee'];
            if (isset($item['startTime']) && $item['startTime'] instanceof \DateTimeInterface) {
                $item['startTime'] = $item['startTime']->format('H:i');
            }
            if (isset($item['endTime']) && $item['endTime'] instanceof \DateTimeInterface) {
                $item['endTime'] = $item['endTime']->format('H:i');
            }
            return $item;
        }, $results);
    }

    public function getByUserId(int $userId): array
    {
        $query = $this->createQueryBuilder('s')
            ->select(
                'c.name AS courtName',
                'c.schedulingFee',
                'cs.startTime',
                'cs.endTime',
                's.scheduledAt',
            )
            ->innerJoin('s.courtSchedule', 'cs')
            ->innerJoin('cs.court', 'c')
            ->where('s.user = :userId')
            ->andWhere('s.scheduledAt > CURRENT_TIMESTAMP()')
            ->setParameter('userId', $userId)
            ->getQuery();

        $results = $query->getArrayResult();

        $formattedResults = array_map(function ($item) {
            $item['schedulingFee'] = (float) $item['schedulingFee'];
            
            if (isset($item['scheduledAt']) && $item['scheduledAt'] instanceof \DateTimeInterface) {
                $item['scheduledAt'] = $item['scheduledAt']->format('Y-m-d');
            }
            if (isset($item['startTime']) && $item['startTime'] instanceof \DateTimeInterface) {
                $item['startTime'] = $item['startTime']->format('H:i');
            }
            if (isset($item['endTime']) && $item['endTime'] instanceof \DateTimeInterface) {
                $item['endTime'] = $item['endTime']->format('H:i');
            }
            return $item;
        }, $results);

        return $formattedResults;
    }
}
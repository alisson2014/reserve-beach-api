<?php

declare(strict_types=1);

namespace App\Repository\CourtScheduleRepository;

use App\Entity\Court;
use App\Entity\CourtSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CourtScheduleRepository extends ServiceEntityRepository implements ICourtScheduleRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourtSchedule::class);
    }

    public function getAll(?int $courtId = null, ?int $dayOfWeek = null): array
    {
        $queryBuilder = $this->createQueryBuilder('cs');
        if (!is_null($courtId)) {
            $queryBuilder->where('cs.court = :courtId')
                ->setParameter('courtId', $courtId);
        }

        if (!is_null($dayOfWeek)) {
            $queryBuilder->andWhere('cs.dayOfWeek = :dayOfWeek')
                ->setParameter('dayOfWeek', $dayOfWeek);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function getById(int $id): ?CourtSchedule
    {
        return $this->find($id);
    }

    public function getByCourtAndDayOfWeek(Court $court, int $dayOfWeek): array
    {
        return $this->createQueryBuilder('cs')
            ->where('cs.court = :court')
            ->andWhere('cs.dayOfWeek = :dayOfWeek')
            ->setParameter('court', $court)
            ->setParameter('dayOfWeek', $dayOfWeek)
            ->getQuery()
            ->getResult();
    }

    public function getByIds(array $ids): array
    {
        return $this->findBy(['id' => $ids]);
    }

    public function add(CourtSchedule $courtSchedule, bool $flush = false): CourtSchedule
    {
        $this->getEntityManager()->persist($courtSchedule);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $courtSchedule;
    }

    public function removeByCourt(Court $court, bool $flush = false): void
    {
        $queryBuilder = $this->createQueryBuilder('cs')
            ->delete()
            ->where('cs.court = :court')
            ->setParameter('court', $court);

        $queryBuilder->getQuery()->execute();

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CourtSchedule $courtSchedule, bool $flush = false): void
    {
        $this->getEntityManager()->remove($courtSchedule);

        if (!$flush) return;

        $this->getEntityManager()->flush();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function findOneByCourtWeekdayTime(int $courtId, int $dayOfWeek, string $time): ?CourtSchedule
    {
        return $this->createQueryBuilder('cs')
            ->where('cs.court = :courtId')
            ->andWhere('cs.dayOfWeek = :dayOfWeek')
            ->andWhere('cs.startTime = :time')
            ->setParameter('courtId', $courtId)
            ->setParameter('dayOfWeek', $dayOfWeek)
            ->setParameter('time', $time)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
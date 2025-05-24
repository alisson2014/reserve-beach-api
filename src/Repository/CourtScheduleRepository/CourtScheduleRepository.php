<?php

declare(strict_types=1);

namespace App\Repository\CourtScheduleRepository;

use App\Entity\CourtSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CourtScheduleRepository extends ServiceEntityRepository implements ICourtScheduleRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourtSchedule::class);
    }

    public function getAll(?int $courtId = null): array
    {
        return $this->createQueryBuilder('cs')
            ->where('cs.court_id = :courtId')
            ->setParameter('courtId', $courtId)
            ->getQuery()
            ->getResult();
    }

    public function getById(int $id): ?CourtSchedule
    {
        return $this->find($id);
    }

    public function add(CourtSchedule $courtSchedule, bool $flush = false): CourtSchedule
    {
        $this->getEntityManager()->persist($courtSchedule);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $courtSchedule;
    }

    public function remove(CourtSchedule $courtSchedule, bool $flush = false): void
    {
        $this->getEntityManager()->remove($courtSchedule);

        if (!$flush) return;

        $this->getEntityManager()->flush();
    }
}
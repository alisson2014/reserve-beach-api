<?php

declare(strict_types=1);

namespace App\Repository\CourtTypeRepository;

use App\Entity\CourtType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CourtType>
 * 
 * @method CourtType|null getById(int $id)
 * @method CourtType add(CourtType $courtType, bool $flush = false): CourtType
 * @method CourtType update(CourtType $courtType, bool $flush = false): CourtType
 * @method void remove(CourtType $entity, bool $flush = false): void
 * @method array getAll(): CourtType[]
 * @method array getByNameLike(): CourtType[]
 */
class CourtTypeRepository extends ServiceEntityRepository implements ICourtTypeRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourtType::class);
    }

    public function getByNameLike(string $name): array
    {
        $name = str_replace(' ', '%', $name);

        return $this->createQueryBuilder('c')
            ->where('LOWER(c.name) LIKE LOWER(:name)')
            ->setParameter('name', '%' . trim($name) . '%')
            ->getQuery()
            ->getResult();
    }

    public function getAll(): array
    {
        return $this->findAll();
    }

    public function getById(int $id): ?CourtType
    {
        return $this->find($id);
    }

    public function add(CourtType $courtType, bool $flush = false): CourtType
    {
        $this->getEntityManager()->persist($courtType);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $courtType;
    }

    public function update(CourtType $courtType, bool $flush = false): CourtType
    {
        $this->getEntityManager()->persist($courtType);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $courtType;
    }

    public function remove(CourtType $courtType, bool $flush = false): void
    {
        $this->getEntityManager()->remove($courtType);

        if (!$flush) return;

        $this->getEntityManager()->flush();
    }
}
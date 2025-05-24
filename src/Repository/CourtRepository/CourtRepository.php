<?php

declare(strict_types=1);

namespace App\Repository\CourtRepository;

use App\Entity\Court;
use App\Entity\CourtType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Court>
 * 
 * @method Court|null getById(int $id)
 * @method Court add(Court $court, bool $flush = false): Court
 * @method Court update(Court $court, bool $flush = false): Court
 * @method Court setActive(Court $court, bool $active, bool $flush = false): Court
 * @method void remove(Court $entity, bool $flush = false): void
 * @method array getAll(): Court[]
 * @method array getByNameLike(): Court[]
 */
class CourtRepository extends ServiceEntityRepository implements ICourtRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Court::class);
    }

    public function getAll(): array
    {
        return $this->findAll();
    }

    public function getActive(?string $name = null, ?CourtType $courtType = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.active = :active')
            ->setParameter('active', true);

        if (!is_null($name)) {
            $name = str_replace(' ', '%', $name);

            $qb->andWhere('c.name LIKE :name')
                ->setParameter('name', trim($name));
        }

        if (!is_null($courtType)) {
            $qb->andWhere('c.courtType = :courtTypeId')
                ->setParameter('courtTypeId', $courtType->getId());
        }

        return $qb->getQuery()->getResult();
    }

    public function getById(int $id): ?Court
    {
        return $this->find($id);
    }

    public function add(Court $court, bool $flush = false): Court
    {
        $this->getEntityManager()->persist($court);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $court;
    }

    public function update(Court $court, bool $flush = false): Court
    {
        $this->getEntityManager()->persist($court);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $court;
    }

    public function setActive(Court $court, bool $active, bool $flush = false): Court
    {
        $court->setActive($active);
        $this->getEntityManager()->persist($court);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $court;
    }

    public function remove(Court $court, bool $flush = false): void
    {
        $this->getEntityManager()->remove($court);

        if (!$flush) return;

        $this->getEntityManager()->flush();
    }
}
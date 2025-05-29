<?php

declare(strict_types=1);

namespace App\Repository\PaymentMethodRepository;

use App\Entity\PaymentMethod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PaymentMethodRepository extends ServiceEntityRepository implements IPaymentMethodRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentMethod::class);
    }

    public function getAll(?string $name = null, ?bool $active = true): array
    {
        $queryBuilder = $this->createQueryBuilder('pm');

        if (!is_null($name)) {
            $name = str_replace(' ', '%', $name);
            $queryBuilder->andWhere('pm.name LIKE :name')
                ->setParameter('name', '%' . trim($name) . '%');
        }

        if (!is_null($active)) {
            $queryBuilder->andWhere('pm.active = :active')
                ->setParameter('active', $active);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function getById(int $id): ?PaymentMethod
    {
        return $this->find($id);
    }

    public function enable(array $ids): void
    {
        $this->setActive($ids);
    }

    public function disable(array $ids): void
    {
        $this->setActive($ids, false);
    }

    private function setActive(array $ids, bool $active = true): void
    {
        $this->createQueryBuilder('pm')
            ->update()
            ->set('pm.active', ':active')
            ->where('pm.id IN (:ids)')
            ->setParameter('active', $active)
            ->setParameter('ids', $ids)
            ->getQuery()
            ->execute();
    }
}
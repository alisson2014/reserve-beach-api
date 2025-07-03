<?php

declare(strict_types=1);

namespace App\Repository\CartItemRepository;

use App\Entity\{CartItem, CourtSchedule, User};
use App\Enum\CartStatus;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartItem>
 */
class CartItemRepository extends ServiceEntityRepository implements ICartItemRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartItem::class);
    }

    public function get(int $id): ?CartItem
    {
        return $this->find($id);
    }

    public function add(CartItem $cartItem, bool $flush = false): CartItem
    {
        $this->getEntityManager()->persist($cartItem);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
        return $cartItem;
    }

    public function remove(CartItem $cartItem, bool $flush = false): CartItem
    {
        $this->getEntityManager()->remove($cartItem);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
        return $cartItem;
    }

    public function disable(CartItem $cartItem, bool $flush = false): CartItem
    {
        $cartItem->setActive(false);
        $this->getEntityManager()->persist($cartItem);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
        return $cartItem;
    }

    public function removeByIds(array $ids, bool $flush = false): void
    {
        $cartItems = $this->findBy(['id' => $ids]);
        foreach ($cartItems as $cartItem) {
            $this->remove($cartItem, false);
        }
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Verifica se um determinado horário de quadra já existe no carrinho ativo de um usuário.
     * Essencial para evitar adicionar itens duplicados.
     */
    public function findOneByUserAndSchedule(User $user, CourtSchedule $courtSchedule, DateTimeImmutable $scheduleDate): ?CartItem
    {
        return $this->createQueryBuilder('ci')
            ->innerJoin('ci.cart', 'c')
            ->where('c.user = :user')
            ->andWhere('c.status = :status')
            ->andWhere('ci.courtSchedule = :courtSchedule')
            ->andWhere('ci.scheduleDate = :scheduleDate')
            ->setParameter('user', $user)
            ->setParameter('status', CartStatus::OPEN)
            ->setParameter('courtSchedule', $courtSchedule)
            ->setParameter('scheduleDate', $scheduleDate)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
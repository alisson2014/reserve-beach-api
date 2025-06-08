<?php

declare(strict_types=1);

namespace App\Repository\CartRepository;

use App\Entity\{Cart, User};
use App\Enum\CartStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CartRepository extends ServiceEntityRepository implements ICartRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    public function all(int $user): array
    {
        return $this->findBy(compact('user'), ['createdAt' => 'DESC']);
    }

    public function getById(int $id): ?Cart
    {
        return $this->find($id);
    }

    public function active(int $user): ?Cart
    {
        $status = CartStatus::OPEN;
        return $this->findOneBy(compact('user', 'status'));
    }

    public function add(Cart $cart, bool $flush = false): Cart
    {
        $this->getEntityManager()->persist($cart);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
        return $cart;
    }

    public function setStatus(Cart $cart, CartStatus $status, bool $flush = false): Cart
    {
        $cart->setStatus($status);
        $this->getEntityManager()->persist($cart);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
        return $cart;
    }
}
<?php

declare(strict_types=1);

namespace App\Repository\CartRepository;

use App\Entity\Cart;
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

/**
     * @param int $cartId
     * @return array
     */
    public function findDetailedItems(int $cartId): array
    {
        $query = $this->createQueryBuilder('cart')
            ->select(
                'court.name AS courtName',
                'court.schedulingFee',
                'item.id AS cartItemId',
                'item.scheduleDate',
                'schedule.startTime', 
                'schedule.endTime'   
            )
            ->innerJoin('cart.items', 'item')
            ->innerJoin('item.courtSchedule', 'schedule')
            ->innerJoin('schedule.court', 'court')
            ->where('cart.id = :cartId')
            ->setParameter('cartId', $cartId)
            ->getQuery();
        $results = $query->getArrayResult();

        $formattedResults = array_map(function ($item) {
            if (isset($item['scheduleDate']) && $item['scheduleDate'] instanceof \DateTimeInterface) {
                $item['scheduleDate'] = $item['scheduleDate']->format('Y-m-d');
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

    /**
     * Prepara um resumo do carrinho para a tela de pagamento.
     *
     * @param int $cartId O ID do carrinho.
     * @return array Retorna um array com os itens e o valor total.
     */
    public function getPaymentSummary(int $cartId): array
    {
        $itemsQuery = $this->createQueryBuilder('cart')
            ->select(
                'court.name as courtName',
                'court.schedulingFee',
                'item.scheduleDate',
                'schedule.startTime',
                'schedule.endTime'
            )
            ->innerJoin('cart.items', 'item')
            ->innerJoin('item.courtSchedule', 'schedule')
            ->innerJoin('schedule.court', 'court')
            ->where('cart.id = :cartId')
            ->setParameter('cartId', $cartId)
            ->getQuery();

        $items = $itemsQuery->getArrayResult();

        $summary = [
            'items' => [],
            'totalAmount' => 0.0,
        ];

        foreach ($items as $item) {
            $scheduleDate = ($item['scheduleDate'] instanceof \DateTimeInterface)
                ? $item['scheduleDate']->format('d/m/Y')
                : 'Data inválida';

            $startTime = ($item['startTime'] instanceof \DateTimeInterface)
                ? $item['startTime']->format('H:i')
                : '';

            $endTime = ($item['endTime'] instanceof \DateTimeInterface)
                ? $item['endTime']->format('H:i')
                : '';

            $summary['items'][] = [
                'courtName' => $item['courtName'],
                'schedule' => sprintf('%s das %s às %s', $scheduleDate, $startTime, $endTime),
                'price' => $item['schedulingFee'],
            ];

            $summary['totalAmount'] += $item['schedulingFee'];
        }

        return $summary;
    }
}
<?php

declare(strict_types=1);

namespace App\Controller\Cart;

use App\Entity\{Cart, CartItem, CourtSchedule};
use App\Repository\CartItemRepository\ICartItemRepository;
use App\Repository\CartRepository\ICartRepository;
use App\Repository\CourtScheduleRepository\ICourtScheduleRepository;
use App\Repository\UserRepository\IUserRepository;
use App\Utils\ResponseUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/carts/active/items')]
#[IsGranted('ROLE_USER')] 
class CartItemController extends AbstractController
{
    use ResponseUtils;

    public function __construct(
        private readonly ICartItemRepository $cartItemRepository,
        private readonly ICartRepository $cartRepository,
        private readonly ICourtScheduleRepository $courtScheduleRepository,
        private readonly IUserRepository $userRepository,
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route(name: 'cart_item_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (is_null($user)) {
            return $this->unauthorized('Usuário não autenticado.');
        }

        $user = $this->userRepository->getByEmail($user->getUserIdentifier());

        $data = $request->toArray();
        $courtScheduleIds = $data['courtScheduleIds'] ?? null;

        if (empty($courtScheduleIds) || !is_array($courtScheduleIds)) {
            return $this->json([
                'status' => false, 
                'errors' => ['courtScheduleIds' => 'Horários são obrigatórios e devem ser um array']
            ], Response::HTTP_BAD_REQUEST);
        }

        $courtSchedules = array_filter($courtScheduleIds, fn($id) => is_int($id) || ctype_digit($id));
        if (count($courtSchedules) !== count($courtScheduleIds)) {
            return $this->json([
                'status' => false, 
                'errors' => ['courtScheduleIds' => 'Todos os IDs de horários devem ser inteiros válidos.']
            ], Response::HTTP_BAD_REQUEST);
        }

        $schedules = $this->courtScheduleRepository->getByIds($courtScheduleIds);

        if (count($schedules) !== count($courtScheduleIds)) {
            $foundIds = array_map(fn(CourtSchedule $s): ?int => $s->getId(), $schedules);
            $notFoundIds = array_diff($courtScheduleIds, $foundIds);
            return $this->json([
                'status' => false, 
                'errors' => ['courtScheduleIds' => 'Os seguintes horários não foram encontrados: ' . implode(', ', $notFoundIds)]
            ], Response::HTTP_NOT_FOUND);
        }
        
        $cart = $this->cartRepository->active($user->getId()) ?? new Cart($user);
        $this->em->persist($cart);

        $items = [];

        foreach ($schedules as $schedule) {
            if ($this->cartItemRepository->findOneByUserAndSchedule($user, $schedule)) {
                return $this->conflict("O horário para a quadra '{$schedule->getCourt()->getName()}' no dia {$schedule->getStartTime()->format('d/m/Y H:i')} já está no seu carrinho.");
            }
            
            // Validação: O horário está disponível?

            $cartItem = new CartItem($cart, $schedule);
            $this->cartItemRepository->add($cartItem);
            $items[] = $cartItem->toArray();
        }

        try {
            $this->courtScheduleRepository->flush(); 
        } catch (\Exception $e) {
            return $this->internalServerError('Erro ao criar os horários: ' . $e->getMessage());
        }

        $ids = array_map(fn(CourtSchedule $schedule): ?int => $schedule->getId(), $courtSchedules);

        return $this->ok($ids, 'Itens adicionados com sucesso.');
    }
}
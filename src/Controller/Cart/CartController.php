<?php

declare(strict_types=1);

namespace App\Controller\Cart;

use App\Entity\Cart;
use App\Enum\CartStatus;
use App\Repository\CartRepository\ICartRepository;
use App\Repository\UserRepository\IUserRepository;
use App\Utils\ResponseUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/carts')]
#[IsGranted('ROLE_USER')]
class CartController extends AbstractController
{
    use ResponseUtils;

    public const NOT_FOUND_MESSAGE = 'Carrinho não encontrado.';

    public function __construct(
        private ICartRepository $cartRepository,
        private IUserRepository $userRepository
    ) {}

    #[Route('/active', name: 'cart', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $user = $this->getUser();

        if (is_null($user)) {
            return $this->unauthorized('Usuário não autenticado.');
        }

        $user = $this->userRepository->getByEmail($user->getUserIdentifier());
        $cart = $this->cartRepository->active($user->getId());

        return $this->ok($cart?->toArray() ?? []);
    }

    #[Route(name: 'cart_create', methods: ['POST'])]
    public function create(): JsonResponse
    {
        $user = $this->getUser();

        if (is_null($user)) {
            return $this->unauthorized('Usuário não autenticado.');
        }

        $user = $this->userRepository->getByEmail($user->getUserIdentifier());

        if (!is_null($this->cartRepository->active($user->getId()))) {
            return $this->conflict('Já existe um carrinho ativo para este usuário.');
        }

        $cart = new Cart($user);

        try {
            $this->cartRepository->add($cart, true);
        } catch (\Exception $ex) {
            return $this->internalServerError('Erro ao cadastrar carrinho: ' . $ex->getMessage());
        }

        return $this->created($cart->toArray(), 'Carrinho criado com sucesso.');
    }

    #[Route('/{id}', name: 'cart_set_status', methods: ['PATCH'])]
    public function setStatus(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $status = $data['status'] ?? null;

        if (is_null($status) || !CartStatus::isValidValue($status)) {
            return $this->json([
                    'status' => false, 
                    'errors' => ['status' => 'Status inválido.']
                ], 
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $user = $this->getUser();
        $cartStatus = CartStatus::from($status);

        if (is_null($user)) {
            return $this->unauthorized('Usuário não autenticado.');
        }

        $cart = $this->cartRepository->getById($id);

        if (is_null($cart)) {
            return $this->notFoundResource(self::NOT_FOUND_MESSAGE);
        }

        if ($cart->getUser()->getEmail() !== $user->getUserIdentifier()) {
            return $this->forbidden('Você não tem permissão para modificar este carrinho.');
        }
        
        try {
            $this->cartRepository->setStatus($cart, $cartStatus, true);
        } catch (\Exception $ex) {
            return $this->internalServerError('Erro ao atualizar o status do carrinho: ' . $ex->getMessage());
        }

        return $this->ok($cart->toArray(), 'Status do carrinho atualizado com sucesso.');
    }

    #[Route('/{cartId}/summary', name: 'cart_payment_summary', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getPaymentSummary(int $cartId): JsonResponse
    {
        $summary = $this->cartRepository->getPaymentSummary($cartId);

        if (empty($summary)) {
            return $this->notFoundResource('Resumo do carrinho não encontrado.');
        }

        return $this->ok($summary, 'Resumo do carrinho obtido com sucesso.');
    }
}
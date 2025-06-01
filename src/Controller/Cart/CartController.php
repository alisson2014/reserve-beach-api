<?php

declare(strict_types=1);

namespace App\Controller\Cart;

use App\Entity\Cart;
use App\Enum\CartStatus;
use App\Repository\CartRepository\ICartRepository;
use App\Repository\UserRepository\IUserRepository;
use App\Utils\ResponseUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/carts')]
class CartController extends AbstractController
{
    use ResponseUtils;

    public const NOT_FOUND_MESSAGE = 'Carrinho não encontrado.';

    public function __construct(
        private ICartRepository $cartRepository, 
        private IUserRepository $userRepository
    ) {}

    #[Route(name: 'cart', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): JsonResponse
    {
        $user = $this->getUser();

        if (is_null($user)) {
            return $this->unauthorized('Usuário não autenticado.');
        }

        $user = $this->userRepository->getByEmail($user->getUserIdentifier());

        $cart = $this->cartRepository->getActive($user->getId());

        if (is_null($cart)) {
            $cart = new Cart();
            $cart->setUser($user);

            try {
                $this->cartRepository->add($cart, true);
            } catch (\Exception $ex) {
                return $this->internalServerError('Erro ao cadastrar carrinho: ' . $ex->getMessage());
            }
        }

        return $this->ok($cart->toArray());
    }

    #[Route('/{id}/open', name: 'cart_set_open', methods: ['PATCH'])]
    #[IsGranted('ROLE_USER')]
    public function setOpen(int $id): JsonResponse
    {
        return $this->setStatus($id, $this->getUser(), CartStatus::OPEN);
    }

    #[Route('/{id}/close', name: 'cart_set_close', methods: ['PATCH'])]
    #[IsGranted('ROLE_USER')]
    public function setClose(int $id): JsonResponse
    {
        return $this->setStatus($id, $this->getUser(), CartStatus::CLOSED);
    }

    #[Route('/{id}/cancel', name: 'cart_set_cancel', methods: ['PATCH'])]
    #[IsGranted('ROLE_USER')]
    public function setCancel(int $id): JsonResponse
    {
        return $this->setStatus($id, $this->getUser(), CartStatus::CANCELED);
    }

    private function setStatus(int $cartId, ?UserInterface $user, CartStatus $cartStatus = CartStatus::OPEN): JsonResponse
    {
        if (is_null($user)) {
            return $this->unauthorized('Usuário não autenticado.');
        }

        $cart = $this->cartRepository->getById($cartId);

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
}
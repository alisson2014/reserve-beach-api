<?php

declare(strict_types=1);

namespace App\Controller\TokenValidation;

use App\Repository\UserRepository\IUserRepository;
use App\Utils\ResponseUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{Request, JsonResponse};

#[Route('/user/token')]
class TokenValidationController extends AbstractController
{
    use ResponseUtils;

    #[Route('/validate', name: 'user_token_validate', methods: ['GET'])]
    public function validateToken(Request $request, JWTTokenManagerInterface $jwtManager, IUserRepository $userRepository): JsonResponse
    {
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return $this->json(['valid' => false, 'message' => 'Token não recebido.'], 401);
        }

        $token = substr($authHeader, 7);

        try {
            $payload = $jwtManager->parse($token);

            if (!$payload) {
                return $this->json(['valid' => false, 'message' => 'Token inválido.'], 401);
            }

            $email = $payload['username'] ?? null;
            if (!$email) {
                return $this->json(['valid' => false, 'message' => 'Payload do token inválido.'], 401);
            }

            $user = $userRepository->getByEmail($email);
            if (!$user) {
                return $this->json(['valid' => false, 'message' => 'Usuário não encontrado.'], 404);
            }

            return $this->json([
                'valid' => true,
                'user' => $user->toArray(),
            ]);
        } catch (\Exception) {
            return $this->json(['valid' => false, 'message' => 'Token inválido ou expirado.'], 401);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Client;

use App\Dto\LoginDto;
use App\Repository\UserRepository\IUserRepository;
use App\Utils\ValidationErrorFormatterTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\{Request, JsonResponse, Response};

class LoginController extends AbstractController 
{
    use ValidationErrorFormatterTrait;

    #[Route('/api/user/login', name: 'user_login', methods: ['POST'])]
    public function index(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager,
        ValidatorInterface $validator,
        IUserRepository $userRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $loginDto = new LoginDto();
        $loginDto->email = $data['email'] ?? null;
        $loginDto->password = $data['password'] ?? null;

        $errors = $validator->validate($loginDto);
        if (count($errors) > 0) {
            return $this->json(['status' => false, 'errors' => $this->formatValidationErrors($errors)], Response::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->getByEmail($loginDto->email);
        if (is_null($user) || !$passwordHasher->isPasswordValid($user, $loginDto->password)) {
            return $this->json(['status' => false, 'message' => 'Credenciais inválidas.'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $jwtManager->create($user);

        return $this->json(['status' => true, ...compact('token')], Response::HTTP_OK);
    }
}
<?php

namespace App\Controller;

use App\Dto\LoginDto;
use App\Repository\UserRepository;
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

    private UserRepository $userRepository; 

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/api/user/login', name: 'user_login', methods: ['POST'])]
    public function index(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $loginDto = new LoginDto();
        $loginDto->email = $data['email'] ?? null;
        $loginDto->password = $data['password'] ?? null;

        $errors = $validator->validate($loginDto);
        if (count($errors) > 0) {
            return $this->json(['errors' => $this->formatValidationErrors($errors)], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->getByEmail($loginDto->email);
        if (is_null($user) || !$passwordHasher->isPasswordValid($user, $loginDto->password)) {
            return $this->json(['errors' => 'Credenciais inválidas.'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $jwtManager->create($user);

        return $this->json(compact('token'), Response::HTTP_OK);
    }
}
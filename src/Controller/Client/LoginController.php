<?php

declare(strict_types=1);

namespace App\Controller\Client;

use App\Dto\LoginDto;
use App\Repository\UserRepository\IUserRepository;
use App\Utils\ResponseUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\{Request, JsonResponse};

#[Route('/user/login')]
class LoginController extends AbstractController 
{
    use ResponseUtils;

    #[Route(name: 'user_login', methods: ['POST'])]
    public function index(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager,
        ValidatorInterface $validator,
        IUserRepository $userRepository
    ): JsonResponse {
        $loginDto = LoginDto::fromArray($request->toArray());

        if (count($errors = $validator->validate($loginDto)) > 0) {
            return $this->badRequest($errors);
        }

        $user = $userRepository->getByEmail($loginDto->email);
        if (is_null($user) || !$passwordHasher->isPasswordValid($user, $loginDto->password)) {
            return $this->unauthorized('Email ou senha inválidos, tente novamente.');
        }

        $userArray = $user->toArray();
        $welcomeMessage = "Bem-vindo(a) {$userArray['name']}!";

        return $this->ok([
            'token' => $jwtManager->create($user),
            'user' => $userArray,
        ], $welcomeMessage);
    }
}
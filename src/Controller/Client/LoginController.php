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
use Symfony\Component\HttpFoundation\{Request, JsonResponse, Response};

class LoginController extends AbstractController 
{
    use ResponseUtils;

    #[Route('/user/login', name: 'user_login', methods: ['POST'])]
    public function index(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager,
        ValidatorInterface $validator,
        IUserRepository $userRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $loginDto = LoginDto::fromArray($data);

        if (count($errors = $validator->validate($loginDto)) > 0) {
            return $this->badRequest($errors);
        }

        $user = $userRepository->getByEmail($loginDto->email);
        if (is_null($user) || !$passwordHasher->isPasswordValid($user, $loginDto->password)) {
            return $this->unauthorized('Credenciais inválidas.');
        }

        return $this->ok([
            'token' => $jwtManager->create($user),
            'user' => $user->toArray()
        ], 'Login realizado com sucesso.');
    }
}
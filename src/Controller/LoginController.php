<?php

namespace App\Controller;

use App\Dto\LoginDto;
use App\Repository\ClientRepository;
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

    private ClientRepository $clientRepository; 

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    #[Route('/api/client/login', name: 'client_login', methods: ['POST'])]
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

        $client = $this->clientRepository->getByEmail($loginDto->email);
        if (is_null($client) || !$passwordHasher->isPasswordValid($client, $loginDto->password)) {
            return $this->json(['errors' => 'Credenciais inválidas.'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $jwtManager->create($client);

        return $this->json(compact('token'), Response::HTTP_OK);
    }
}
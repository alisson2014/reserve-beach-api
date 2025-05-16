<?php

namespace App\Controller;

use InvalidArgumentException;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Component\HttpFoundation\{Request, JsonResponse, Response};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class ClientController extends AbstractController
{
    private ClientRepository $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    #[Route('/api/client/register', name: 'client_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data['email'] || !$data['password']) {
            return $this->json(['error' => 'Email e senha são obrigatórios.'], Response::HTTP_BAD_REQUEST);
        }

        if ($this->clientRepository->getByEmail($data['email'])) {
            return $this->json(['error' => 'Email já cadastrado.'], Response::HTTP_CONFLICT);
        }
        
        try {
            $client = new Client();
            $client->setName($data['name']);
            $client->setLastName($data['lastName']);
            $client->setEmail($data['email']);
            $client->setPassword(
                $passwordHasher->hashPassword($client, $data['password'])
            );
        } catch (InvalidArgumentException $ex) {
            return $this->json(['error' => $ex->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $ex) {
            return $this->json(['error' => 'Erro ao cadastrar cliente.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->clientRepository->add($client, true);

        return $this->json(['message' => 'Client registered successfully.'], Response::HTTP_CREATED);
    }

    #[Route('/api/client/login', name: 'client_login', methods: ['POST'])]
    public function login(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data['email'] || !$data['password']) {
            return $this->json(['error' => 'Email e senha são obrigatórios.'], Response::HTTP_BAD_REQUEST);
        }

        $client = $this->clientRepository->getByEmail($data['email']);
        if (!$client || !$passwordHasher->isPasswordValid($client, $data['password'])) {
            return $this->json(['error' => 'Credenciais inválidas.'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $jwtManager->create($client);

        return $this->json(['token' => $token]);
    }
}
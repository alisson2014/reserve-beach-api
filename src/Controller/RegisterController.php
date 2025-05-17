<?php

namespace App\Controller;

use App\Dto\RegisterDto;
use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Utils\ValidationErrorFormatterTrait;
use Symfony\Component\HttpFoundation\{Request, JsonResponse, Response};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterController extends AbstractController
{
    use ValidationErrorFormatterTrait;

    private ClientRepository $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    #[Route('/api/client/register', name: 'client_register', methods: ['POST'])]
    public function index(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $registerDto = new RegisterDto();
        $registerDto->name = $data['name'] ?? null;
        $registerDto->lastName = $data['lastName'] ?? null;
        $registerDto->email = $data['email'] ?? null;
        $registerDto->password = $data['password'] ?? null;
        $registerDto->confirmPassword = $data['confirmPassword'] ?? null;

        $errors = $validator->validate($registerDto);
        if (count($errors) > 0) {
            return $this->json(['errors' => $this->formatValidationErrors($errors)], Response::HTTP_BAD_REQUEST);
        }

        if ($this->clientRepository->getByEmail($data['email'])) {
            return $this->json(['errors' => 'Email já cadastrado.'], Response::HTTP_CONFLICT);
        }

        $client = Client::getFromRegisterDto($registerDto);
        $client->setPassword(
            $passwordHasher->hashPassword($client, $data['password'])
        );

        try {
            $this->clientRepository->add($client, true);
        } catch (\Exception $ex) {
            return $this->json(['error' => 'Erro ao cadastrar cliente.' . $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([
            'message' => "Cliente {$client->getName()} cadastrado com sucesso!",
            'data' => $client->toArray(),
        ], Response::HTTP_CREATED);
    }
}
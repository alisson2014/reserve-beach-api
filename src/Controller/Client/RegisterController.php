<?php

declare(strict_types=1);

namespace App\Controller\Client;

use App\Dto\RegisterDto;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\ValidationErrorFormatterTrait;
use Symfony\Component\HttpFoundation\{Request, JsonResponse, Response};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterController extends AbstractController
{
    use ValidationErrorFormatterTrait;

    #[Route('/api/client/register', name: 'user_register', methods: ['POST'])]
    public function index(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        UserRepository $userRepository
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

        if ($userRepository->getByEmail($data['email'])) {
            return $this->json(['errors' => 'Email já cadastrado.'], Response::HTTP_CONFLICT);
        }

        $user = User::getFromRegisterDto($registerDto);
        $user->setPassword(
            $passwordHasher->hashPassword($user, $data['password'])
        );

        try {
            $userRepository->add($user, true);
        } catch (\Exception $ex) {
            return $this->json(['error' => 'Erro ao cadastrar cliente.' . $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(
            [
                'message' => "Cliente {$user->getName()} cadastrado com sucesso!",
                'data' => $user->toArray(),
            ], 
            Response::HTTP_CREATED
        );
    }
}
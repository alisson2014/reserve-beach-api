<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Dto\RegisterDto;
use App\Entity\User;
use App\Enum\Position;
use App\Repository\UserRepository;
use App\Utils\ValidationErrorFormatterTrait;
use Symfony\Component\HttpFoundation\{Request, JsonResponse, Response};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RegisterController extends AbstractController
{
    use ValidationErrorFormatterTrait;

    #[Route('/api/admin/register', name: 'admin_register', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
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

        $user = $this->getUser();
        $email = $user->getUserIdentifier();
        $user = $userRepository->getByEmail($email);

        if ($user?->getPosition() !== Position::MANAGER) {
            return $this->json(['errors' => 'Você não tem permissão para cadastrar outros administradores.'], Response::HTTP_FORBIDDEN);
        }

        if ($userRepository->getByEmail($data['email'])) {
            return $this->json(['errors' => 'Email já cadastrado.'], Response::HTTP_CONFLICT);
        }

        $user = User::getFromRegisterDto($registerDto);
        $user->setPassword(
            $passwordHasher->hashPassword($user, $data['password'])
        );
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPosition(Position::from($data['position'] ?? Position::EMPLOYEE->value));

        try {
            $userRepository->add($user, true);
        } catch (\Exception $ex) {
            return $this->json(['error' => 'Erro ao cadastrar administrador.' . $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(
            [
                'message' => "Administrador {$user->getName()} cadastrado com sucesso!",
                'data' => $user->toArray(),
            ], 
            Response::HTTP_CREATED
        );
    }
}
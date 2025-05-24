<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Dto\RegisterDto;
use App\Entity\User;
use App\Repository\UserRepository\IUserRepository;
use App\Utils\ResponseUtils;
use Symfony\Component\HttpFoundation\{Request, JsonResponse};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RegisterController extends AbstractController
{
    use ResponseUtils;

    #[Route('/api/admin/register', name: 'admin_register', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        IUserRepository $userRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $registerDto = RegisterDto::fromArray($data);

        if (count($errors = $validator->validate($registerDto)) > 0) {
            return $this->badRequest($errors);
        }

        if ($userRepository->getByEmail($data['email'])) {
            return $this->conflict('Email já cadastrado.');
        }

        $user = User::get($registerDto);
        $user->setPassword(
            $passwordHasher->hashPassword($user, $data['password'])
        );
        $user->setRoles(['ROLE_ADMIN']);

        try {
            $userRepository->add($user, true);
        } catch (\Exception $ex) {
            return $this->internalServerError('Erro ao cadastrar administrador: ' . $ex->getMessage());
        }

        return $this->created($user->toArray(), "Administrador {$user->getName()} cadastrado com sucesso!");
    }
}
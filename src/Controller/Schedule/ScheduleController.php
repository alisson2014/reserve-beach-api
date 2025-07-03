<?php

declare(strict_types=1);

namespace App\Controller\Schedule;

use App\Dto\ScheduleDto;
use App\Entity\Schedule;
use App\Repository\ScheduleRepository\IScheduleRepository;
use App\Repository\UserRepository\IUserRepository;
use App\Utils\ResponseUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, JsonResponse, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/schedules')]
class ScheduleController extends AbstractController
{
    use ResponseUtils;

    public const NOT_FOUND_MESSAGE = 'Agendamento não encontrado.';

    public function __construct(
        private IScheduleRepository $scheduleRepository,
        private IUserRepository $userRepository
    ) {}

    #[Route(name: 'schedules', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = $request->toArray();
        $user = $this->getUser();
        if (!$user) {
            return $this->unauthorized('Usuário não autenticado.');
        }

        $user = $this->userRepository->getByEmail($user->getUserIdentifier());

        $schedules = [];
        foreach ($data as $item) {
            $item['userId'] = $user->getId();

            $scheduleDto = ScheduleDto::fromArray($item);
            $validationErrors = $validator->validate($scheduleDto);
            if (count($validationErrors) > 0) {
                return $this->badRequest($validationErrors);
            }

            $schedule = Schedule::get($scheduleDto, em: $this->scheduleRepository->getEntityManager());
            $schedules[] = $schedule;
            $this->scheduleRepository->add($schedule);
        }

        return $this->json($schedules);
    }
}
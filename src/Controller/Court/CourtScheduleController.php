<?php

declare(strict_types=1);

namespace App\Controller\Court;

use App\Dto\CourtScheduleDto;
use App\Entity\CourtSchedule;
use App\Repository\CourtRepository\ICourtRepository;
use App\Repository\CourtScheduleRepository\ICourtScheduleRepository;
use App\Utils\ResponseUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{Request, JsonResponse, Response};
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/court_schedules')]
class CourtScheduleController extends AbstractController
{
    public const string NOT_FOUND_MESSAGE = 'Horário não encontrado.';
    
    use ResponseUtils;

    public function __construct(
        private ICourtScheduleRepository $courtScheduleRepository, 
        private ICourtRepository $courtRepository
    ) {}

    #[Route('/{id}', name: 'court_schedules', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function index(int $id, Request $request): JsonResponse
    {
        $schedules = $this->courtScheduleRepository->getAll($id, $request->query->getInt('dayOfWeek', 0) ?: null);

        if (empty($schedules)) {
            return $this->notFoundResource(self::NOT_FOUND_MESSAGE);
        }

        $schedulesArray = array_map(
            fn (CourtSchedule $schedule): array => $schedule->toArray(),
            $schedules
        );

        return $this->ok($schedulesArray);
    }

    #[Route(name: 'court_schedules_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = $request->toArray();

        if (!is_array($data) || empty($data)) {
            return $this->json(['status' => false, 'message' => 'O payload deve ser um array de horários.'], Response::HTTP_BAD_REQUEST);
        }

        $courtSchedules = [];

        foreach ($data as $scheduleData) {
            $courtScheduleDto = CourtScheduleDto::fromArray($scheduleData);

            $validationErrors = $validator->validate($courtScheduleDto);
            if (count($validationErrors) > 0) {
                return $this->badRequest($validationErrors);
            }

            $court = $this->courtRepository->getById($courtScheduleDto->courtId);
            if (is_null($court)) {
                return $this->notFoundResource(self::NOT_FOUND_MESSAGE . "ID: {$courtScheduleDto->courtId}.");
            }

            $courtSchedule = CourtSchedule::get($courtScheduleDto, $court);
            $courtSchedules[] = $courtSchedule;
            $this->courtScheduleRepository->add($courtSchedule, false);
        }

        try {
            $this->courtScheduleRepository->flush(); 
        } catch (\Exception $e) {
            return $this->internalServerError('Erro ao criar os horários: ' . $e->getMessage());
        }

        $ids = array_map(fn(CourtSchedule $schedule): ?int => $schedule->getId(), $courtSchedules);

        return $this->ok($ids, 'Horários criados com sucesso.');
    }

    #[Route('/{courtId}/court', name: 'court_schedules_delete_by_court', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteByCourt(int $courtId): JsonResponse
    {
        $court = $this->courtRepository->getById($courtId);
        if (is_null($court)) {
            return $this->notFoundResource(self::NOT_FOUND_MESSAGE . "ID: {$courtId}.");
        }

        $this->courtScheduleRepository->removeByCourt($court);
        return $this->ok([], 'Horários removidos com sucesso.');
    }
}
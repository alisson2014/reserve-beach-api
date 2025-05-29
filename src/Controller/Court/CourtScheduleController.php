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

class CourtScheduleController extends AbstractController
{
    use ResponseUtils;

    public function __construct(
        private ICourtScheduleRepository $courtScheduleRepository, 
        private ICourtRepository $courtRepository
    ) {}

    #[Route('/api/court_schedules/{id}', name: 'court_schedules', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function index(int $id, Request $request): JsonResponse
    {
        $schedules = $this->courtScheduleRepository->getAll($id, $request->query->getInt('dayOfWeek', 0) ?: null);

        if (empty($schedules)) {
            return $this->notFoundResource('Nenhum horário encontrado para esta quadra.');
        }

        $schedulesArray = array_map(
            fn ($schedule) => $schedule->toArray(),
            $schedules
        );

        return $this->ok($schedulesArray);
    }

    #[Route('/api/court_schedules', name: 'court_schedules_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

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
                return $this->notFoundResource("Quadra não encontrada para o ID: {$courtScheduleDto->courtId}.");
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

    #[Route('/api/court_schedules/delete', name: 'court_schedules_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data) || empty($data)) {
            return $this->json(['status' => false, 'message' => 'O payload deve ser um array não vázio com os ids para exclusão.'], Response::HTTP_BAD_REQUEST);
        }

        $deleted = 0;
        foreach ($data as $idDelete) {
            $courtSchedule = $this->courtScheduleRepository->getById($idDelete);

            if (is_null($courtSchedule)) {
                return $this->notFoundResource("Horário não encontrado para o ID: {$idDelete}.");
            }

            $this->courtScheduleRepository->remove($courtSchedule, false);
            $deleted++; 
        }

        if ($deleted > 0) {
            $this->courtScheduleRepository->flush();
        }

        return $this->ok((string)$deleted, 'Horários removidos com sucesso.');
    }
}
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

    private ICourtScheduleRepository $courtScheduleRepository;

    private ICourtRepository $courtRepository;

    public function __construct(ICourtScheduleRepository $courtScheduleRepository, ICourtRepository $courtRepository)
    {
        $this->courtScheduleRepository = $courtScheduleRepository;
        $this->courtRepository = $courtRepository;
    }

    #[Route('/api/court_schedules/{id}', name: 'court_schedules', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function index(int $id): JsonResponse
    {
        $schedules = $this->courtScheduleRepository->getAll($id);

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

        $errors = [];
        $courtSchedules = [];

        foreach ($data as $index => $scheduleData) {
            $courtScheduleDto = CourtScheduleDto::fromArray($scheduleData);

            $validationErrors = $validator->validate($courtScheduleDto);
            if (count($validationErrors) > 0) {
                $errors[$index] = $validationErrors;
                continue;
            }

            $court = $this->courtRepository->getById($courtScheduleDto->courtId);
            if (is_null($court)) {
                $errors[$index] = ['Quadra não encontrada para o ID informado.'];
                continue;
            }

            $courtSchedule = CourtSchedule::get($courtScheduleDto, $court);
            $courtSchedules[] = $courtSchedule;
            $this->courtScheduleRepository->add($courtSchedule, false);
        }

        // if (!empty($errors)) {
        //     return $this->json(['status' => false, 'erros' => $errors], Response::HTTP_BAD_REQUEST);
        // }

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

        if (!is_array($data)) {
            return $this->json(['status' => false, 'message' => 'O payload deve ser um array de horários para exclusão.'], Response::HTTP_BAD_REQUEST);
        }

        $errors = [];
        $deleted = 0;

        foreach ($data as $index => $deleteData) {
            if (
                empty($deleteData['courtId']) ||
                empty($deleteData['weekday']) ||
                empty($deleteData['time'])
            ) {
                $errors[$index] = 'courtId, weekday e time são obrigatórios.';
                continue;
            }

            $court = $this->courtRepository->getById($deleteData['courtId']);
            if (is_null($court)) {
                $errors[$index] = 'Quadra não encontrada para o ID informado.';
                continue;
            }

            $schedule = $this->courtScheduleRepository->findOneByCourtWeekdayTime(
                $deleteData['courtId'],
                $deleteData['weekday'],
                $deleteData['time']
            );

            if (!$schedule) {
                $errors[$index] = 'Horário não encontrado para os dados informados.';
                continue;
            }

            $this->courtScheduleRepository->remove($schedule, false);
            $deleted++;
        }

        if (!empty($errors)) {
            return $this->json(['status' => false, 'deleted' => $deleted, 'erros' => $errors], Response::HTTP_BAD_REQUEST);
        }

        if ($deleted > 0) {
            $this->courtScheduleRepository->flush();
        }

        return $this->ok(['deleted' => $deleted], 'Horários removidos com sucesso.');
    }
}
<?php

declare(strict_types=1);

namespace App\Controller\Court;

use App\Dto\CourtDto;
use App\Entity\Court;
use App\Repository\CourtRepository\ICourtRepository;
use App\Repository\CourtTypeRepository\ICourtTypeRepository;
use App\Utils\ValidationErrorFormatterTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, JsonResponse, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CourtController extends AbstractController
{
    use ValidationErrorFormatterTrait;

    private ICourtRepository $courtRepository;

    private ICourtTypeRepository $courtTypeRepository;

    public function __construct(ICourtRepository $courtRepository, ICourtTypeRepository $courtTypeRepository)
    {
        $this->courtRepository = $courtRepository;
        $this->courtTypeRepository = $courtTypeRepository;
    }

    #[Route('/api/courts', name: 'courts', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $name = $request->query->get('name');
        $type = $request->query->get('type');

        $courtType = $this->courtTypeRepository->getById(intval($type));

        $courts = array_map(
            fn (Court $court): array => $court->toArray(),
            $this->courtRepository->getActive($name, $courtType)
        );

        return $this->json(['status' => true, 'data' => $courts]);
    }

    #[Route('/api/courts/{id}', name: 'courts_show', methods: ['GET'],  requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        $court = $this->courtRepository->getById($id);

        if (is_null($court)) {
            return $this->json(['status' => true, 'message' => 'Quadra não encontrada.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['status' => true, 'data' => $court->toArray()]);
    }

    #[Route('/api/courts', name: 'court_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $courtDto = new CourtDto();
        $courtDto->name = $data['name'] ?? null;
        $courtDto->description = $data['description'] ?? null;
        $courtDto->schedulingFee = $data['schedulingFee'] ?? null;
        $courtDto->capacity = $data['capacity'] ?? null;
        $courtDto->active = $data['active'] ?? true;
        $courtDto->courtTypeId = isset($data['courtTypeId']) && !empty($data['courtTypeId']) ? (int)$data['courtTypeId'] : null;

        $errors = $validator->validate($courtDto);
        if (count($errors) > 0) {
            return $this->json(['status' => false, 'errors' => $this->formatValidationErrors($errors)], Response::HTTP_BAD_REQUEST);
        }

        $courtType = $this->courtTypeRepository->getById($courtDto->courtTypeId);
        if (is_null($courtType)) {
            return $this->json(['status' => false, 'message' => 'Quadra não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $court = Court::get($courtDto, $courtType);

        try {
            $this->courtRepository->add($court, true);
        } catch (\Exception $ex) {
            return $this->json([
                'status' => false,
                'message' => 'Erro ao cadastrar quadra' . $ex->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['status' => true, 'data' => $court->toArray()], Response::HTTP_CREATED);
    }

    #[Route('/api/courts/{id}', name: 'court_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(int $id, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $courtDto = new CourtDto();
        $courtDto->name = $data['name'] ?? null;
        $courtDto->description = $data['description'] ?? null;
        $courtDto->schedulingFee = $data['schedulingFee'] ?? null;
        $courtDto->capacity = $data['capacity'] ?? null;
        $courtDto->active = $data['active'] ?? true;
        $courtDto->courtTypeId = isset($data['courtTypeId']) && !empty($data['courtTypeId']) ? (int)$data['courtTypeId'] : null;

        $errors = $validator->validate($courtDto);
        if (count($errors) > 0) {
            return $this->json(['status' => false, 'errors' => $this->formatValidationErrors($errors)], Response::HTTP_BAD_REQUEST);
        }

        $courtType = $this->courtTypeRepository->getById($courtDto->courtTypeId);
        if (is_null($courtType)) {
            return $this->json(['status' => false, 'message' => 'Tipo de quadra não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $court = $this->courtRepository->getById($id);

        if (is_null($court)) {
            return $this->json(['status' => false, 'message' => 'Quadra não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $court->setName($courtDto->name);
        $court->setDescription($courtDto->description);
        $court->setSchedulingFee($courtDto->schedulingFee);
        $court->setCapacity($courtDto->capacity);
        $court->setActive($courtDto->active);
        $court->setCourtType($courtType);

        try {
            $court = $this->courtRepository->update($court, true);
        } catch (\Exception $ex) {
            return $this->json([
                'status' => false,
                'message' => 'Erro ao atualizar quadra' . $ex->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['status' => true, 'data' => $court->toArray()]);
    }

    #[Route('/api/courts/{id}', name: 'court_delete', methods: ['DELETE'],  requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): JsonResponse
    {
        $court = $this->courtRepository->getById($id);

        if (is_null($court)) {
            return $this->json(['status' => false, 'message' => 'Quadra não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->courtRepository->remove($court, true);
        } catch (\Exception $ex) {
            return $this->json([
                'status' => false,
                'message' => 'Erro ao remover quadra' . $ex->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
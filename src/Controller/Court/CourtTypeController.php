<?php

declare(strict_types=1);

namespace App\Controller\Court;

use App\Dto\CourtTypeDto;
use App\Entity\CourtType;
use App\Repository\CourtTypeRepository\ICourtTypeRepository;
use App\Utils\ResponseUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\{Request, JsonResponse, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CourtTypeController extends AbstractController
{
    use ResponseUtils;

    const NOT_FOUND_MESSAGE = 'Tipo de quadra não encontrado.';

    private ICourtTypeRepository $courtTypeRepository;

    public function __construct(ICourtTypeRepository $courtTypeRepository)
    {
        $this->courtTypeRepository = $courtTypeRepository;
    }

    #[Route('/api/court_types', name: 'court_types', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $name = $request->query->get('name');
        if ($name) {
            $courtTypes = array_map(
                fn (CourtType $courtType): array => $courtType->toArray(),
                $this->courtTypeRepository->getByNameLike($name)
            );
        } else {
            $courtTypes = array_map(
                fn (CourtType $courtType): array => $courtType->toArray(),
                $this->courtTypeRepository->getAll()
            );
        }

        return $this->json(['status' => true, 'data' => $courtTypes]);
    }

    #[Route('/api/court_types/{id}', name: 'court_types_show', methods: ['GET'],  requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        $courtType = $this->courtTypeRepository->getById($id);

        if (is_null($courtType)) {
            return $this->notFoundResource(self::NOT_FOUND_MESSAGE);
        }

        return $this->json(['status' => true, 'data' => $courtType->toArray()]);
    }

    #[Route('/api/court_types', name: 'court_types_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $courtTypeDto = CourtTypeDto::fromArray($data);

        if (count($errors = $validator->validate($courtTypeDto)) > 0) {
            return $this->badRequest($errors);
        }

        $courtType = CourtType::get($courtTypeDto);

        try {
            $this->courtTypeRepository->add($courtType, true);
        } catch (\Exception $ex) {
            return $this->json([
                'status' => false,
                'message' => 'Erro ao cadastrar tipo de quadra' . $ex->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['status' => true, 'data' => $courtType->toArray()], Response::HTTP_CREATED);
    }

    #[Route('/api/court_types/{id}', name: 'court_type_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(int $id, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $courtTypeDto = CourtTypeDto::fromArray($data);

        if (count($errors = $validator->validate($courtTypeDto)) > 0) {
            return $this->badRequest($errors);
        }

        $courtType = $this->courtTypeRepository->getById($id);
        if (is_null($courtType)) {
            return $this->notFoundResource(self::NOT_FOUND_MESSAGE);
        }

        $courtType->setName($courtTypeDto->name);

        try {
            $courtType = $this->courtTypeRepository->update($courtType, true);
        } catch (\Exception $ex) {
            return $this->json([
                'status' => false,
                'message' => 'Erro ao atualizar tipo de quadra' . $ex->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['status' => true, 'data' => $courtType->toArray()]);
    }

    #[Route('/api/court_types/{id}', name: 'court_type_delete', methods: ['DELETE'],  requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): JsonResponse
    {
        $courtType = $this->courtTypeRepository->getById($id);

        if (is_null($courtType)) {
            return $this->notFoundResource(self::NOT_FOUND_MESSAGE);
        }

        try {
            $this->courtTypeRepository->remove($courtType, true);
        } catch (\Exception $ex) {
            return $this->json([
                'status' => false,
                'message' => 'Erro ao remover tipo de quadra' . $ex->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
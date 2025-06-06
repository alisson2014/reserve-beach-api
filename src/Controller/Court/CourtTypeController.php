<?php

declare(strict_types=1);

namespace App\Controller\Court;

use App\Dto\CourtTypeDto;
use App\Entity\CourtType;
use App\Repository\CourtTypeRepository\ICourtTypeRepository;
use App\Utils\ResponseUtils;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\{Request, JsonResponse, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/court_types')]
class CourtTypeController extends AbstractController
{
    use ResponseUtils;

    public const NOT_FOUND_MESSAGE = 'Tipo de quadra não encontrado.';

    public function __construct(
        private ICourtTypeRepository $courtTypeRepository
    ) {}

    #[Route(name: 'court_types', methods: ['GET'])]
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

        return $this->ok($courtTypes);
    }

    #[Route('/{id}', name: 'court_types_show', methods: ['GET'],  requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        $courtType = $this->courtTypeRepository->getById($id);

        if (is_null($courtType)) {
            return $this->notFoundResource(self::NOT_FOUND_MESSAGE);
        }

        return $this->ok($courtType->toArray());
    }

    #[Route(name: 'court_types_create', methods: ['POST'])]
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
        } catch (UniqueConstraintViolationException $ex) {
            return $this->conflict('Já existe um tipo de quadra com esse nome.');
        }  catch (\Exception $ex) {
            return $this->internalServerError('Erro ao cadastrar tipo de quadra: ' . $ex->getMessage());
        }

        return $this->created($courtType->toArray(), 'Tipo de quadra cadastrado com sucesso!');
    }

    #[Route('/{id}', name: 'court_type_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
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

        $courtType = CourtType::get($courtTypeDto, $courtType); 

        try {
            $courtType = $this->courtTypeRepository->update($courtType, true);
        } catch (UniqueConstraintViolationException $ex) {
            return $this->conflict('Já existe um tipo de quadra com esse nome.');
        } catch (\Exception $ex) {
            return $this->internalServerError('Erro ao atualizar tipo de quadra: ' . $ex->getMessage());  
        }

        return $this->ok($courtType->toArray(), 'Tipo de quadra atualizado com sucesso!');
    }

    #[Route('/{id}', name: 'court_type_delete', methods: ['DELETE'],  requirements: ['id' => '\d+'])]
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
            return $this->internalServerError('Erro ao remover tipo de quadra: ' . $ex->getMessage());
        }

        return $this->noContent();
    }
}
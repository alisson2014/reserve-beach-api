<?php

declare(strict_types=1);

namespace App\Controller\Court;

use App\Dto\CourtDto;
use App\Entity\Court;
use App\Entity\CourtType;
use App\Repository\CourtRepository\ICourtRepository;
use App\Repository\CourtTypeRepository\ICourtTypeRepository;
use App\Utils\ResponseUtils;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, JsonResponse, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/courts')]
class CourtController extends AbstractController
{
    use ResponseUtils;

    public const NOT_FOUND_MESSAGE = 'Quadra não encontrada.';

    public function __construct(
        private ICourtRepository $courtRepository, 
        private ICourtTypeRepository $courtTypeRepository,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route(name: 'courts', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $name = $request->query->get('name');
        $type = $request->query->get('type') ?: CourtType::BEACH_TENNIS;
        $active = $request->query->get('active');

        $courtType = $this->courtTypeRepository->getById(intval($type));

        $courts = array_map(
            fn (Court $court): array => $court->toArray(),
            $this->courtRepository->all($name, $courtType, $active)
        );

        return $this->ok($courts);
    }

    #[Route('/{id}', name: 'courts_show', methods: ['GET'],  requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        $court = $this->courtRepository->getById($id);

        if (is_null($court)) {
            return $this->notFoundResource(self::NOT_FOUND_MESSAGE);
        }

        return $this->ok($court->toArray());
    }

    #[Route(name: 'court_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $courtDto = CourtDto::fromArray($request->toArray());

        if (count($errors = $validator->validate($courtDto)) > 0) {
            return $this->badRequest($errors);
        }

        $courtType = $this->courtTypeRepository->getById($courtDto->courtTypeId);
        if (is_null($courtType)) {
            return $this->notFoundResource(CourtTypeController::NOT_FOUND_MESSAGE);
        }

        $court = Court::get($courtDto, $courtType);

        try {
            $this->courtRepository->add($court, true);
        } catch (UniqueConstraintViolationException $ex) {
            return $this->conflict('Já existe uma quadra com esse nome.');
        } catch (\Exception $ex) {
            return $this->internalServerError('Erro ao cadastrar quadra: ' . $ex->getMessage());
        }

        return $this->created($court->toArray(), 'Quadra cadastrada com sucesso.');
    }

    #[Route('/{id}', name: 'court_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(int $id, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $courtDto = CourtDto::fromArray($request->toArray());

        if (count($errors = $validator->validate($courtDto)) > 0) {
            return $this->badRequest($errors);
        }

        $courtType = $this->courtTypeRepository->getById($courtDto->courtTypeId);
        if (is_null($courtType)) {
            return $this->notFoundResource(CourtTypeController::NOT_FOUND_MESSAGE);
        }

        $court = $this->courtRepository->getById($id);

        if (is_null($court)) {
            return $this->notFoundResource(self::NOT_FOUND_MESSAGE);
        }

        $court = Court::get($courtDto, $courtType, $court);

        try {
            $court = $this->courtRepository->update($court, true);
        } catch (UniqueConstraintViolationException $ex) {
            return $this->conflict('Já existe uma quadra com esse nome.');
        } catch (\Exception $ex) {
            return $this->internalServerError('Erro ao atualizar quadra: ' . $ex->getMessage());
        }

        return $this->ok($court->toArray(), 'Quadra atualizada com sucesso.');
    }

    #[Route('/active', name: 'court_set_active', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function setActive(Request $request): JsonResponse
    {
        $data = $request->toArray();

        $ids = $data['ids'] ?? [];
        $active = $data['active'] ?? null;

        if (!is_bool($active)) {
            return $this->json([
                'status' => false,
                'errors' => [
                    'active' => ['Campo ativo deve ser um booleano.']
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            foreach ($ids as $id) {
                $court = $this->courtRepository->getById($id);

                if (is_null($court)) {
                    return $this->notFoundResource(self::NOT_FOUND_MESSAGE . " (ID: $id)");
                }

                $this->courtRepository->setActive($court, $active);
            }
        } catch (\Exception $ex) {
            return $this->internalServerError('Erro ao atualizar quadras: ' . $ex->getMessage());
        }

        $this->entityManager->flush();

        $newStatus = $active ? 'ativadas' : 'desativadas';

        return $this->ok([], sprintf('Quadras %s com sucesso.', $newStatus));
    }

    #[Route('/{id}', name: 'court_delete', methods: ['DELETE'],  requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): JsonResponse
    {
        $court = $this->courtRepository->getById($id);

        if (is_null($court)) {
            return $this->notFoundResource(self::NOT_FOUND_MESSAGE);
        }

        try {
            $this->courtRepository->remove($court, true);
        } catch (\Exception $ex) {
            return $this->internalServerError('Erro ao remover quadra: ' . $ex->getMessage());
        }

        return $this->noContent();
    }
}
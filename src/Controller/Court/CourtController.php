<?php

declare(strict_types=1);

namespace App\Controller\Court;

use App\Entity\Court;
use App\Repository\CourtRepository\ICourtRepository;
use App\Utils\ValidationErrorFormatterTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, JsonResponse, Response};
use Symfony\Component\Routing\Annotation\Route;

class CourtController extends AbstractController
{
    use ValidationErrorFormatterTrait;

    private ICourtRepository $courtRepository;

    public function __construct(ICourtRepository $courtRepository)
    {
        $this->courtRepository = $courtRepository;
    }

    #[Route('/api/courts', name: 'courts', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $name = $request->query->get('name');
        $type = $request->query->get('type');

        $courts = array_map(
            fn (Court $court): array => $court->toArray(),
            $this->courtRepository->getAll()
        );

        return $this->json(['status' => true, 'data' => $courts]);
    }
}
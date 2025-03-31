<?php

namespace App\Controller;

use App\Entity\Teste;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    #[Route('/')]
    public function index(): Response
    {
        return new Response("Rota principal funcionando!");
    }

    #[Route('/api/v1/test')]
    public function test(EntityManagerInterface $entityManager): JsonResponse
    {
        $allTests = $entityManager->getRepository(Teste::class)->findAll();

        // Transforma os objetos em arrays associativos
        $data = array_map(function (Teste $test) {
            return [
                'id' => $test->getId(),
                'nome' => $test->getNome(),
                'dad' => 'a'
            ];
        }, $allTests);

        return new JsonResponse([
            'message' => 'API funcionando!',
            'status' => 'success',
            'code' => 200,
            'data' => $data
        ]);
    }
}
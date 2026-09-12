<?php

namespace App\Controller;

use App\Entity\Client;
use App\Service\FavoriService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur de la wishlist (favoris).
 */
class FavoriController extends ApiController
{
    public function __construct(
        private readonly FavoriService $favoriService,
    ) {
    }

    /**
     * Liste les favoris du client connecté.
     */
    #[Route('/api/favoris', name: 'api_favori_list', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')]
    public function list(): JsonResponse
    {
        $client = $this->getUser();
        if (!$client instanceof Client) {
            return $this->json(['message' => 'Accès refusé.'], Response::HTTP_FORBIDDEN);
        }

        return $this->json([
            'items' => $this->favoriService->listForClient($client),
            'ids' => $this->favoriService->getProductIdsForClient($client),
        ]);
    }

    /**
     * Ajoute un produit aux favoris.
     */
    #[Route('/api/favoris/{refProduit}', name: 'api_favori_add', methods: ['POST'], requirements: ['refProduit' => '\d+'])]
    #[IsGranted('ROLE_CLIENT')]
    public function add(int $refProduit): JsonResponse
    {
        $client = $this->getUser();
        if (!$client instanceof Client) {
            return $this->json(['message' => 'Accès refusé.'], Response::HTTP_FORBIDDEN);
        }

        try {
            $favori = $this->favoriService->add($client, $refProduit);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($favori, Response::HTTP_CREATED);
    }

    /**
     * Retire un produit des favoris.
     */
    #[Route('/api/favoris/{refProduit}', name: 'api_favori_remove', methods: ['DELETE'], requirements: ['refProduit' => '\d+'])]
    #[IsGranted('ROLE_CLIENT')]
    public function remove(int $refProduit): JsonResponse
    {
        $client = $this->getUser();
        if (!$client instanceof Client) {
            return $this->json(['message' => 'Accès refusé.'], Response::HTTP_FORBIDDEN);
        }

        try {
            $this->favoriService->remove($client, $refProduit);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['message' => 'Produit retiré des favoris.']);
    }
}

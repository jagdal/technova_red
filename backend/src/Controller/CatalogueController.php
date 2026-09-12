<?php

namespace App\Controller;

use App\Service\ProductSearchService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Catalogue produits (recherche, filtres, SEO).
 */
#[Route('/api/catalogue/produits')]
class CatalogueController extends ApiController
{
    public function __construct(
        private readonly ProductSearchService $productSearchService,
    ) {
    }

    /**
     * Recherche et filtre le catalogue produits (pagination).
     */
    #[Route('', name: 'api_catalogue_produits', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $q = $request->query->get('q');
        $refCategorie = $request->query->has('ref_categorie')
            ? (int) $request->query->get('ref_categorie')
            : null;
        $prixMin = $request->query->has('prix_min') ? (float) $request->query->get('prix_min') : null;
        $prixMax = $request->query->has('prix_max') ? (float) $request->query->get('prix_max') : null;
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = min(50, max(1, (int) $request->query->get('limit', 12)));

        return $this->json($this->productSearchService->search(
            is_string($q) ? $q : null,
            $refCategorie,
            $prixMin,
            $prixMax,
            $page,
            $limit,
        ));
    }

    /**
     * Détail produit par identifiant.
     */
    #[Route('/{id}', name: 'api_catalogue_produit_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        try {
            $produit = $this->productSearchService->findById($id);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json($produit);
    }

    /**
     * Détail produit par slug SEO (meta title/description inclus).
     */
    #[Route('/slug/{slug}', name: 'api_catalogue_produit_slug', methods: ['GET'])]
    public function bySlug(string $slug): JsonResponse
    {
        try {
            $produit = $this->productSearchService->findBySlug($slug);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json($produit);
    }
}

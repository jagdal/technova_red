<?php

namespace App\Service;

use App\Data\ProductImages;
use App\Entity\Produit;
use App\Repository\AvisRepository;
use App\Repository\ProduitRepository;

/**
 * Service de recherche et filtrage du catalogue produits.
 */
class ProductSearchService
{
    public function __construct(
        private readonly ProduitRepository $produitRepository,
        private readonly AvisRepository $avisRepository,
        private readonly SlugService $slugService,
    ) {
    }

    /**
     * Recherche des produits avec filtres et pagination.
     *
     * @return array{items: list<array<string, mixed>>, total: int, page: int, limit: int}
     */
    public function search(
        ?string $q,
        ?int $refCategorie,
        ?float $prixMin,
        ?float $prixMax,
        int $page = 1,
        int $limit = 12,
    ): array {
        $result = $this->produitRepository->searchWithFilters($q, $refCategorie, $prixMin, $prixMax, $page, $limit);

        $items = array_map(fn (Produit $p) => $this->serializeProduit($p), $result['items']);

        return [
            'items' => $items,
            'total' => $result['total'],
            'page' => $page,
            'limit' => $limit,
        ];
    }

    /**
     * Retourne un produit par identifiant.
     *
     * @return array<string, mixed>
     */
    public function findById(int $refProduit): array
    {
        $produit = $this->produitRepository->findOneWithCategory($refProduit);
        if (!$produit instanceof Produit) {
            throw new \InvalidArgumentException('Produit introuvable.');
        }

        return $this->serializeProduit($produit, true);
    }

    /**
     * Retourne un produit par son slug SEO.
     *
     * @return array<string, mixed>
     */
    public function findBySlug(string $slug): array
    {
        foreach ($this->produitRepository->findIdAndLabels() as $row) {
            if ($this->slugService->slugify($row['libelleProduit']) === $slug) {
                return $this->findById((int) $row['refProduit']);
            }
        }

        throw new \InvalidArgumentException('Produit introuvable.');
    }

    /**
     * Génère les entrées du sitemap produits.
     *
     * @return list<array{slug: string, libelle: string}>
     */
    public function getSitemapEntries(): array
    {
        $entries = [];
        foreach ($this->produitRepository->findAll() as $produit) {
            $entries[] = [
                'slug' => $this->slugService->slugify($produit->getLibelleProduit()),
                'libelle' => $produit->getLibelleProduit(),
            ];
        }

        return $entries;
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeProduit(Produit $produit, bool $withSeo = false): array
    {
        $data = [
            'ref_produit' => $produit->getRefProduit(),
            'libelle_produit' => $produit->getLibelleProduit(),
            'description' => $produit->getDescription(),
            'prix' => $produit->getPrix(),
            'stock' => $produit->getStock(),
            'url_image' => ProductImages::resolve(
                $produit->getUrlImage(),
                $produit->getLibelleProduit(),
                $produit->getCategorie()->getNomCategorie(),
            ),
            'ref_categorie' => $produit->getCategorie()->getRefCategorie(),
            'nom_categorie' => $produit->getCategorie()->getNomCategorie(),
        ];

        if ($withSeo) {
            $data['slug'] = $this->slugService->slugify($produit->getLibelleProduit());
            $data['meta_title'] = $produit->getLibelleProduit().' | TechNova';
            $data['meta_description'] = mb_substr(strip_tags($produit->getDescription()), 0, 160);
        }

        $stats = $this->avisRepository->getStatsForProduit($produit);
        $data['note_moyenne'] = $stats['moyenne'];
        $data['nb_avis'] = $stats['nb_avis'];

        return $data;
    }
}

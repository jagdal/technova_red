<?php

namespace App\Service;

use App\Data\ProductImages;
use App\Entity\Client;
use App\Entity\Favori;
use App\Entity\Produit;
use App\Repository\FavoriRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service de gestion des favoris (wishlist).
 */
class FavoriService
{
    public function __construct(
        private readonly FavoriRepository $favoriRepository,
        private readonly ProduitRepository $produitRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listForClient(Client $client): array
    {
        return array_map(
            fn (Favori $favori) => $this->serializeFavori($favori),
            $this->favoriRepository->findByClientOrdered($client),
        );
    }

    /**
     * @return list<int>
     */
    public function getProductIdsForClient(Client $client): array
    {
        return $this->favoriRepository->findProductIdsByClient($client);
    }

    /**
     * @return array<string, mixed>
     */
    public function add(Client $client, int $refProduit): array
    {
        $produit = $this->findProduitOrFail($refProduit);

        if ($this->favoriRepository->findOneByClientAndProduit($client, $produit) instanceof Favori) {
            throw new \InvalidArgumentException('Ce produit est déjà dans vos favoris.');
        }

        $favori = (new Favori())
            ->setClient($client)
            ->setProduit($produit)
            ->setDateAjout(new \DateTime());

        $this->entityManager->persist($favori);
        $this->entityManager->flush();

        return $this->serializeFavori($favori);
    }

    public function remove(Client $client, int $refProduit): void
    {
        $produit = $this->findProduitOrFail($refProduit);
        $favori = $this->favoriRepository->findOneByClientAndProduit($client, $produit);

        if (!$favori instanceof Favori) {
            throw new \InvalidArgumentException('Ce produit n\'est pas dans vos favoris.');
        }

        $this->entityManager->remove($favori);
        $this->entityManager->flush();
    }

    private function findProduitOrFail(int $refProduit): Produit
    {
        $produit = $this->produitRepository->find($refProduit);
        if (!$produit instanceof Produit) {
            throw new \InvalidArgumentException('Produit introuvable.');
        }

        return $produit;
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeFavori(Favori $favori): array
    {
        $produit = $favori->getProduit();

        return [
            'id_favori' => $favori->getIdFavori(),
            'ref_produit' => $produit->getRefProduit(),
            'libelle_produit' => $produit->getLibelleProduit(),
            'prix' => $produit->getPrix(),
            'stock' => $produit->getStock(),
            'nom_categorie' => $produit->getCategorie()->getNomCategorie(),
            'url_image' => ProductImages::resolve(
                $produit->getUrlImage(),
                $produit->getLibelleProduit(),
                $produit->getCategorie()->getNomCategorie(),
            ),
            'date_ajout' => $favori->getDateAjout()->format('Y-m-d'),
        ];
    }
}

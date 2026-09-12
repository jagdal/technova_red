<?php

namespace App\Service;

use App\Entity\Avis;
use App\Entity\Client;
use App\Entity\Produit;
use App\Repository\AvisRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service de gestion des avis clients.
 */
class AvisService
{
    public function __construct(
        private readonly AvisRepository $avisRepository,
        private readonly ProduitRepository $produitRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return array{items: list<array<string, mixed>>, moyenne: float|null, nb_avis: int}
     */
    public function getByProduct(int $refProduit): array
    {
        $produit = $this->produitRepository->find($refProduit);
        if (!$produit instanceof Produit) {
            throw new \InvalidArgumentException('Produit introuvable.');
        }

        $stats = $this->avisRepository->getStatsForProduit($produit);
        $items = array_map(
            fn (Avis $avis) => $this->serializeAvis($avis),
            $this->avisRepository->findByProduitOrdered($produit),
        );

        return [
            'items' => $items,
            'moyenne' => $stats['moyenne'],
            'nb_avis' => $stats['nb_avis'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function create(Client $client, int $refProduit, int $note, string $commentaire): array
    {
        $produit = $this->produitRepository->find($refProduit);
        if (!$produit instanceof Produit) {
            throw new \InvalidArgumentException('Produit introuvable.');
        }

        if ($this->avisRepository->findOneByClientAndProduit($client, $produit) instanceof Avis) {
            throw new \InvalidArgumentException('Vous avez déjà noté ce produit.');
        }

        $avis = (new Avis())
            ->setClient($client)
            ->setProduit($produit)
            ->setNote($note)
            ->setCommentaire(trim($commentaire))
            ->setDateAvis(new \DateTime());

        $this->entityManager->persist($avis);
        $this->entityManager->flush();

        return $this->serializeAvis($avis);
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeAvis(Avis $avis): array
    {
        return [
            'id_avis' => $avis->getIdAvis(),
            'ref_produit' => $avis->getProduit()->getRefProduit(),
            'note' => $avis->getNote(),
            'commentaire' => $avis->getCommentaire(),
            'date_avis' => $avis->getDateAvis()->format('Y-m-d H:i'),
            'auteur' => $avis->getClient()->getPrenomClient().' '.mb_substr($avis->getClient()->getNomClient(), 0, 1).'.',
        ];
    }
}

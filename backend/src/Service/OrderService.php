<?php

namespace App\Service;

use App\Data\ProductImages;
use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\Contenir;
use App\Entity\Produit;
use App\Enum\CommandeStatut;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service de gestion des commandes.
 */
class OrderService
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly ProduitRepository $produitRepository,
        private readonly CommandeRepository $commandeRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Valide le panier et crée une commande en base.
     *
     * @return array<string, mixed>
     */
    public function checkout(Client $client): array
    {
        $cartDetails = $this->cartService->getCartDetails();
        if ([] === $cartDetails['items']) {
            throw new \InvalidArgumentException('Le panier est vide.');
        }

        $this->entityManager->beginTransaction();

        try {
            $commande = (new Commande())
                ->setClient($client)
                ->setDateCommande(new \DateTime())
                ->setStatut(CommandeStatut::EN_ATTENTE->value)
                ->setTotal($cartDetails['total']);

            foreach ($cartDetails['items'] as $item) {
                $produit = $this->produitRepository->find($item['ref_produit']);
                if (!$produit instanceof Produit) {
                    throw new \InvalidArgumentException('Produit introuvable dans le panier.');
                }

                if ($produit->getStock() < $item['quantite']) {
                    throw new \InvalidArgumentException(sprintf(
                        'Stock insuffisant pour le produit "%s".',
                        $produit->getLibelleProduit()
                    ));
                }

                $produit->setStock($produit->getStock() - $item['quantite']);

                $ligne = (new Contenir())
                    ->setCommande($commande)
                    ->setProduit($produit)
                    ->setQuantite($item['quantite'])
                    ->setPrixUnitaire($item['prix_unitaire']);

                $commande->getContenus()->add($ligne);
                $this->entityManager->persist($ligne);
            }

        $this->entityManager->persist($commande);
        $this->entityManager->flush();
        $this->entityManager->commit();
    } catch (\Throwable $e) {
        $this->entityManager->rollback();
        throw $e;
    }

    return $this->serializeCommande($commande);
  }

    /**
     * Met à jour le statut d'une commande (admin).
     */
    public function updateStatus(Commande $commande, string $statut): array
    {
        if (!in_array($statut, array_column(CommandeStatut::cases(), 'value'), true)) {
            throw new \InvalidArgumentException('Statut de commande invalide.');
        }

        $commande->setStatut($statut);
        $this->entityManager->flush();

        return $this->serializeCommande($commande);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getClientOrders(Client $client): array
    {
        $commandes = $this->commandeRepository->findBy(
            ['client' => $client],
            ['idCommande' => 'DESC']
        );

        return array_map(fn (Commande $c) => $this->serializeCommande($c), $commandes);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getAllOrdersForAdmin(): array
    {
        $commandes = $this->commandeRepository->findBy([], ['idCommande' => 'DESC']);

        return array_map(fn (Commande $c) => $this->serializeCommande($c), $commandes);
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeCommande(Commande $commande): array
    {
        $lignes = [];
        foreach ($commande->getContenus() as $contenu) {
            $produit = $contenu->getProduit();
            $lignes[] = [
                'ref_produit' => $produit->getRefProduit(),
                'libelle_produit' => $produit->getLibelleProduit(),
                'url_image' => ProductImages::resolve(
                    $produit->getUrlImage(),
                    $produit->getLibelleProduit(),
                    $produit->getCategorie()->getNomCategorie(),
                ),
                'quantite' => $contenu->getQuantite(),
                'prix_unitaire' => $contenu->getPrixUnitaire(),
            ];
        }

        $paiement = $commande->getPaiement();
        $paiementData = null;
        if ($paiement !== null) {
            $paiementData = [
                'mode_paiement' => $paiement->getModePaiement(),
                'date_paiement' => $paiement->getDatePaiement()->format('Y-m-d'),
                'montant' => $paiement->getMontant(),
            ];
        }

        return [
            'id_commande' => $commande->getIdCommande(),
            'date_commande' => $commande->getDateCommande()->format('Y-m-d'),
            'total' => $commande->getTotal(),
            'statut' => $commande->getStatut(),
            'id_client' => $commande->getClient()->getIdClient(),
            'lignes' => $lignes,
            'paiement' => $paiementData,
        ];
    }
}

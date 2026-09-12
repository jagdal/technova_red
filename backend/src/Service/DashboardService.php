<?php

namespace App\Service;

use App\Repository\ClientRepository;
use App\Repository\CommandeRepository;
use App\Repository\MessageContactRepository;
use App\Repository\ProduitRepository;

/**
 * Service du tableau de bord administrateur.
 */
class DashboardService
{
    public function __construct(
        private readonly CommandeRepository $commandeRepository,
        private readonly ProduitRepository $produitRepository,
        private readonly ClientRepository $clientRepository,
        private readonly MessageContactRepository $messageContactRepository,
    ) {
    }

    /**
     * Retourne les indicateurs clés du tableau de bord admin.
     *
     * @return array<string, mixed>
     */
    public function getOverview(): array
    {
        $commandes = $this->commandeRepository->findAll();
        $produits = $this->produitRepository->findAll();

        $chiffreAffaires = 0.0;
        foreach ($commandes as $commande) {
            $chiffreAffaires += (float) $commande->getTotal();
        }

        $stockFaible = array_filter(
            $produits,
            static fn ($p) => $p->getStock() < 5
        );

        return [
            'nb_commandes' => count($commandes),
            'nb_produits' => count($produits),
            'nb_clients' => count($this->clientRepository->findAll()),
            'messages_non_lus' => $this->messageContactRepository->countUnread(),
            'chiffre_affaires' => number_format($chiffreAffaires, 2, '.', ''),
            'produits_stock_faible' => count($stockFaible),
            'dernieres_commandes' => array_map(
                static fn ($c) => [
                    'id_commande' => $c->getIdCommande(),
                    'date_commande' => $c->getDateCommande()->format('Y-m-d'),
                    'total' => $c->getTotal(),
                    'statut' => $c->getStatut(),
                ],
                array_slice(array_reverse($commandes), 0, 5)
            ),
        ];
    }
}

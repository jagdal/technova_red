<?php

namespace App\Service;

use App\DTO\PaymentDto;
use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\Paiement;
use App\Enum\CommandeStatut;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;

/**
 * Service de gestion des paiements.
 */
class PaymentService
{
    public function __construct(
        private readonly CommandeRepository $commandeRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly CartService $cartService,
    ) {
    }

    /**
     * Enregistre le paiement d'une commande.
     *
     * @return array<string, mixed>
     */
    public function processPayment(PaymentDto $dto, Client $client): array
    {
        $commande = $this->commandeRepository->find($dto->idCommande);
        if (!$commande instanceof Commande) {
            throw new \InvalidArgumentException('Commande introuvable.');
        }

        if ($commande->getClient()->getIdClient() !== $client->getIdClient()) {
            throw new \InvalidArgumentException('Cette commande ne vous appartient pas.');
        }

        if (null !== $commande->getPaiement()) {
            throw new \InvalidArgumentException('Cette commande a déjà été payée.');
        }

        $paiement = (new Paiement())
            ->setCommande($commande)
            ->setModePaiement($dto->modePaiement)
            ->setDatePaiement(new \DateTime())
            ->setMontant($commande->getTotal());

        $commande->setPaiement($paiement);
        $commande->setStatut(CommandeStatut::CONFIRMEE->value);

        $this->entityManager->persist($paiement);
        $this->entityManager->flush();
        $this->cartService->clear();

        return [
            'ref_paiement' => $paiement->getRefPaiement(),
            'mode_paiement' => $paiement->getModePaiement(),
            'date_paiement' => $paiement->getDatePaiement()->format('Y-m-d'),
            'montant' => $paiement->getMontant(),
            'id_commande' => $commande->getIdCommande(),
            'statut_commande' => $commande->getStatut(),
        ];
    }

    /**
     * Confirme une commande après paiement Stripe (retour utilisateur).
     *
     * @return array<string, mixed>
     */
    public function confirmStripePayment(Session $session, Client $client): array
    {
        $commande = $this->resolveCommandeFromStripeSession($session);

        if ($commande->getClient()->getIdClient() !== $client->getIdClient()) {
            throw new \InvalidArgumentException('Cette commande ne vous appartient pas.');
        }

        return $this->finalizeStripePayment($commande, $session);
    }

    /**
     * Confirme une commande via webhook Stripe (sans contexte client).
     *
     * @return array<string, mixed>
     */
    public function confirmStripePaymentFromWebhook(Session $session): array
    {
        $commande = $this->resolveCommandeFromStripeSession($session);

        return $this->finalizeStripePayment($commande, $session);
    }

    /**
     * @return array<string, mixed>
     */
    private function finalizeStripePayment(Commande $commande, Session $session): array
    {
        if ('paid' !== $session->payment_status) {
            throw new \InvalidArgumentException('Le paiement Stripe n\'est pas encore confirmé.');
        }

        if (null !== $commande->getPaiement()) {
            $paiement = $commande->getPaiement();

            return [
                'ref_paiement' => $paiement->getRefPaiement(),
                'mode_paiement' => $paiement->getModePaiement(),
                'date_paiement' => $paiement->getDatePaiement()->format('Y-m-d'),
                'montant' => $paiement->getMontant(),
                'id_commande' => $commande->getIdCommande(),
                'statut_commande' => $commande->getStatut(),
                'stripe_session_id' => $session->id,
                'already_paid' => true,
            ];
        }

        $paiement = (new Paiement())
            ->setCommande($commande)
            ->setModePaiement('stripe')
            ->setDatePaiement(new \DateTime())
            ->setMontant($commande->getTotal())
            ->setReferenceTransaction($session->payment_intent ?? $session->id);

        $commande->setPaiement($paiement);
        $commande->setStatut(CommandeStatut::CONFIRMEE->value);

        $this->entityManager->persist($paiement);
        $this->entityManager->flush();
        $this->cartService->clear();

        return [
            'ref_paiement' => $paiement->getRefPaiement(),
            'mode_paiement' => $paiement->getModePaiement(),
            'date_paiement' => $paiement->getDatePaiement()->format('Y-m-d'),
            'montant' => $paiement->getMontant(),
            'id_commande' => $commande->getIdCommande(),
            'statut_commande' => $commande->getStatut(),
            'stripe_session_id' => $session->id,
            'already_paid' => false,
        ];
    }

    private function resolveCommandeFromStripeSession(Session $session): Commande
    {
        $idCommande = (int) ($session->metadata['id_commande'] ?? 0);
        if ($idCommande <= 0) {
            throw new \InvalidArgumentException('Session Stripe invalide : commande introuvable.');
        }

        $commande = $this->commandeRepository->find($idCommande);
        if (!$commande instanceof Commande) {
            throw new \InvalidArgumentException('Commande introuvable.');
        }

        return $commande;
    }
}

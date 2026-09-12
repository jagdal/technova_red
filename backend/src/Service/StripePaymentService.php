<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\Commande;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;
use UnexpectedValueException;

/**
 * Intégration Stripe Checkout pour le paiement des commandes.
 */
class StripePaymentService
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly string $stripeSecretKey,
        private readonly string $stripeWebhookSecret,
        private readonly string $frontendUrl,
    ) {
    }

    /**
     * Crée une session Stripe Checkout pour une commande en attente.
     *
     * @return array{session_id: string, checkout_url: string}
     */
    public function createCheckoutSession(Commande $commande, Client $client): array
    {
        if ('' === trim($this->stripeSecretKey)) {
            throw new \RuntimeException('Stripe n\'est pas configuré (STRIPE_SECRET_KEY manquante).');
        }

        if ($commande->getClient()->getIdClient() !== $client->getIdClient()) {
            throw new \InvalidArgumentException('Cette commande ne vous appartient pas.');
        }

        if (null !== $commande->getPaiement()) {
            throw new \InvalidArgumentException('Cette commande a déjà été payée.');
        }

        Stripe::setApiKey($this->stripeSecretKey);

        $lineItems = [];
        foreach ($commande->getContenus() as $contenu) {
            $produit = $contenu->getProduit();
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $produit->getLibelleProduit(),
                    ],
                    'unit_amount' => (int) round((float) $contenu->getPrixUnitaire() * 100),
                ],
                'quantity' => $contenu->getQuantite(),
            ];
        }

        $successUrl = rtrim($this->frontendUrl, '/').'/commande/confirmation?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = rtrim($this->frontendUrl, '/').'/checkout?cancelled=1';

        $session = Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'customer_email' => $client->getEmail(),
            'metadata' => [
                'id_commande' => (string) $commande->getIdCommande(),
                'id_client' => (string) $client->getIdClient(),
            ],
        ]);

        if (!is_string($session->url) || '' === $session->url) {
            throw new \RuntimeException('Impossible de créer la session Stripe Checkout.');
        }

        return [
            'session_id' => $session->id,
            'checkout_url' => $session->url,
        ];
    }

    /**
     * Confirme le paiement après retour Stripe (page succès).
     *
     * @return array<string, mixed>
     */
    public function confirmCheckoutSession(string $sessionId, Client $client): array
    {
        if ('' === trim($this->stripeSecretKey)) {
            throw new \RuntimeException('Stripe n\'est pas configuré (STRIPE_SECRET_KEY manquante).');
        }

        Stripe::setApiKey($this->stripeSecretKey);
        $session = Session::retrieve($sessionId);

        return $this->paymentService->confirmStripePayment($session, $client);
    }

    /**
     * Traite le webhook Stripe (checkout.session.completed).
     *
     * @return array<string, mixed>|null
     */
    public function handleWebhook(string $payload, ?string $signatureHeader): ?array
    {
        if ('' === trim($this->stripeWebhookSecret)) {
            return null;
        }

        try {
            $event = Webhook::constructEvent($payload, $signatureHeader ?? '', $this->stripeWebhookSecret);
        } catch (UnexpectedValueException|SignatureVerificationException) {
            throw new \InvalidArgumentException('Signature webhook Stripe invalide.');
        }

        if ('checkout.session.completed' !== $event->type) {
            return null;
        }

        /** @var Session $session */
        $session = $event->data->object;

        return $this->paymentService->confirmStripePaymentFromWebhook($session);
    }
}

<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO de création d'une session Stripe Checkout.
 */
class StripeCheckoutDto
{
    #[Assert\NotNull(message: 'L\'identifiant de commande est obligatoire.')]
    #[Assert\Positive]
    public ?int $idCommande = null;
}

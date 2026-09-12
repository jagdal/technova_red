<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO de paiement d'une commande.
 */
class PaymentDto
{
    #[Assert\NotNull(message: 'L\'identifiant de commande est obligatoire.')]
    #[Assert\Positive]
    public ?int $idCommande = null;

    #[Assert\NotBlank(message: 'Le mode de paiement est obligatoire.')]
    #[Assert\Length(max: 50)]
    public string $modePaiement = '';
}

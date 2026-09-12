<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO pour ajouter ou modifier un article du panier.
 */
class CartItemDto
{
    #[Assert\NotNull(message: 'La référence produit est obligatoire.')]
    #[Assert\Positive(message: 'La référence produit doit être positive.')]
    public ?int $refProduit = null;

    #[Assert\NotNull(message: 'La quantité est obligatoire.')]
    #[Assert\Positive(message: 'La quantité doit être supérieure à zéro.')]
    public ?int $quantite = null;
}

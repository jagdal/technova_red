<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO de création d'un avis produit.
 */
class AvisDto
{
    #[Assert\NotNull(message: 'Le produit est obligatoire.')]
    #[Assert\Positive(message: 'Identifiant produit invalide.')]
    public ?int $refProduit = null;

    #[Assert\NotNull(message: 'La note est obligatoire.')]
    #[Assert\Range(min: 1, max: 5, notInRangeMessage: 'La note doit être entre {{ min }} et {{ max }}.')]
    public ?int $note = null;

    #[Assert\NotBlank(message: 'Le commentaire est obligatoire.')]
    #[Assert\Length(max: 500, maxMessage: 'Le commentaire ne peut pas dépasser {{ limit }} caractères.')]
    public string $commentaire = '';
}

<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO de mise à jour du statut d'une commande (admin).
 */
class OrderStatusUpdateDto
{
    #[Assert\NotBlank(message: 'Le statut est obligatoire.')]
    #[Assert\Length(max: 50)]
    public string $statut = '';
}

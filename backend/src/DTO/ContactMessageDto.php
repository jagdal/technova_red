<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO du formulaire de contact public.
 */
class ContactMessageDto
{
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(max: 100)]
    public string $nom = '';

    #[Assert\NotBlank(message: 'L\'email est obligatoire.')]
    #[Assert\Email(message: 'L\'email n\'est pas valide.')]
    #[Assert\Length(max: 150)]
    public string $email = '';

    #[Assert\NotBlank(message: 'Le sujet est obligatoire.')]
    #[Assert\Length(max: 200)]
    public string $sujet = '';

    #[Assert\NotBlank(message: 'Le message est obligatoire.')]
    #[Assert\Length(min: 10, max: 5000)]
    public string $message = '';
}

<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO de connexion (client ou administrateur).
 */
class LoginDto
{
    #[Assert\NotBlank(message: 'L\'email est obligatoire.')]
    #[Assert\Email(message: 'L\'email n\'est pas valide.')]
    public string $email = '';

    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire.')]
    public string $motDePasse = '';

    #[Assert\NotBlank(message: 'Le token CSRF est obligatoire.')]
    public string $csrfToken = '';
}

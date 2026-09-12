<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO d'inscription d'un nouveau client.
 */
class RegisterClientDto
{
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(max: 100)]
    public string $nomClient = '';

    #[Assert\NotBlank(message: 'Le prénom est obligatoire.')]
    #[Assert\Length(max: 100)]
    public string $prenomClient = '';

    #[Assert\NotBlank(message: 'L\'email est obligatoire.')]
    #[Assert\Email(message: 'L\'email n\'est pas valide.')]
    #[Assert\Length(max: 150)]
    public string $email = '';

    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire.')]
    #[Assert\Length(min: 8, max: 255, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.')]
    public string $motDePasse = '';

    #[Assert\NotBlank(message: 'Le téléphone est obligatoire.')]
    #[Assert\Length(max: 20)]
    public string $telephone = '';

    #[Assert\NotBlank(message: 'L\'adresse est obligatoire.')]
    public string $adresse = '';

    #[Assert\NotBlank(message: 'Le token CSRF est obligatoire.')]
    public string $csrfToken = '';
}

<?php

namespace App\Security\Voter;

use App\Entity\Categorie;
use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\Produit;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Contrôle d'accès aux opérations sur les produits.
 *
 * @extends Voter<string, Produit|null>
 */
class ProduitVoter extends Voter
{
    public const VIEW = 'PRODUIT_VIEW';
    public const CREATE = 'PRODUIT_CREATE';
    public const EDIT = 'PRODUIT_EDIT';
    public const DELETE = 'PRODUIT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::CREATE, self::EDIT, self::DELETE], true)
            && ($subject instanceof Produit || null === $subject);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            self::VIEW => true,
            self::CREATE, self::EDIT, self::DELETE => in_array('ROLE_ADMIN', $token->getRoleNames(), true),
            default => false,
        };
    }
}

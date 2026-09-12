<?php

namespace App\Security\Voter;

use App\Entity\Categorie;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Contrôle d'accès aux opérations sur les catégories.
 *
 * @extends Voter<string, Categorie|null>
 */
class CategorieVoter extends Voter
{
    public const VIEW = 'CATEGORIE_VIEW';
    public const CREATE = 'CATEGORIE_CREATE';
    public const EDIT = 'CATEGORIE_EDIT';
    public const DELETE = 'CATEGORIE_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::CREATE, self::EDIT, self::DELETE], true)
            && ($subject instanceof Categorie || null === $subject);
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

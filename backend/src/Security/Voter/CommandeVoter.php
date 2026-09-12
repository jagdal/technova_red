<?php

namespace App\Security\Voter;

use App\Entity\Client;
use App\Entity\Commande;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Contrôle d'accès aux commandes.
 *
 * @extends Voter<string, Commande>
 */
class CommandeVoter extends Voter
{
    public const VIEW = 'COMMANDE_VIEW';
    public const EDIT_STATUS = 'COMMANDE_EDIT_STATUS';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT_STATUS], true)
            && $subject instanceof Commande;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return match ($attribute) {
            self::VIEW => $this->canView($user, $subject),
            self::EDIT_STATUS => in_array('ROLE_ADMIN', $token->getRoleNames(), true),
            default => false,
        };
    }

    private function canView(mixed $user, Commande $commande): bool
    {
        if (in_array('ROLE_ADMIN', $user?->getRoles() ?? [], true)) {
            return true;
        }

        if ($user instanceof Client) {
            return $commande->getClient()->getIdClient() === $user->getIdClient();
        }

        return false;
    }
}

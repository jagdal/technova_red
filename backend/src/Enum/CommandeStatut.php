<?php

namespace App\Enum;

/**
 * Statuts possibles d'une commande.
 */
enum CommandeStatut: string
{
    case EN_ATTENTE = 'en_attente';
    case CONFIRMEE = 'confirmee';
    case EXPEDIEE = 'expediee';
    case LIVREE = 'livree';
    case ANNULEE = 'annulee';
}

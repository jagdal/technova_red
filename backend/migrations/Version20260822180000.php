<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajoute la référence transaction Stripe sur les paiements.
 */
final class Version20260822180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute reference_transaction sur la table paiement (Stripe).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE paiement ADD reference_transaction VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE paiement DROP reference_transaction');
    }
}

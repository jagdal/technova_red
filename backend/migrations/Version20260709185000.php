<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260709185000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add message_contact table for contact form';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE message_contact (id_message INT AUTO_INCREMENT NOT NULL, nom_expediteur VARCHAR(100) NOT NULL, email VARCHAR(150) NOT NULL, sujet VARCHAR(200) NOT NULL, contenu LONGTEXT NOT NULL, date_envoi DATETIME NOT NULL, lu TINYINT(1) NOT NULL, PRIMARY KEY(id_message)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE message_contact');
    }
}

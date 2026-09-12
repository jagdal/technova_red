<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260711120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute les tables avis (notes produits) et favori (wishlist)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE avis (id_avis INT AUTO_INCREMENT NOT NULL, id_client INT NOT NULL, ref_produit INT NOT NULL, note SMALLINT NOT NULL, commentaire LONGTEXT NOT NULL, date_avis DATETIME NOT NULL, INDEX IDX_8B27C52BE173EDC1 (id_client), INDEX IDX_8B27C52BEDB1BFF7 (ref_produit), UNIQUE INDEX uniq_avis_client_produit (id_client, ref_produit), PRIMARY KEY(id_avis)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_AVIS_ID_CLIENT FOREIGN KEY (id_client) REFERENCES client (id_client) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_AVIS_REF_PRODUIT FOREIGN KEY (ref_produit) REFERENCES produit (ref_produit) ON DELETE CASCADE');

        $this->addSql('CREATE TABLE favori (id_favori INT AUTO_INCREMENT NOT NULL, id_client INT NOT NULL, ref_produit INT NOT NULL, date_ajout DATETIME NOT NULL, INDEX IDX_7A7C0E2BE173EDC1 (id_client), INDEX IDX_7A7C0E2BEDB1BFF7 (ref_produit), UNIQUE INDEX uniq_favori_client_produit (id_client, ref_produit), PRIMARY KEY(id_favori)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favori ADD CONSTRAINT FK_FAVORI_ID_CLIENT FOREIGN KEY (id_client) REFERENCES client (id_client) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favori ADD CONSTRAINT FK_FAVORI_REF_PRODUIT FOREIGN KEY (ref_produit) REFERENCES produit (ref_produit) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_AVIS_ID_CLIENT');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_AVIS_REF_PRODUIT');
        $this->addSql('DROP TABLE avis');
        $this->addSql('ALTER TABLE favori DROP FOREIGN KEY FK_FAVORI_ID_CLIENT');
        $this->addSql('ALTER TABLE favori DROP FOREIGN KEY FK_FAVORI_REF_PRODUIT');
        $this->addSql('DROP TABLE favori');
    }
}

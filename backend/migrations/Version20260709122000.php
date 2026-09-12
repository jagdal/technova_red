<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260709122000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema for TechNova (Client, Admin, Categorie, Produit, Commande, Contenir, Paiement)';
    }

    public function up(Schema $schema): void
    {
        // This migration targets MySQL/MariaDB.
        $this->addSql('CREATE TABLE admin (matricule_admin INT AUTO_INCREMENT NOT NULL, nom_admin VARCHAR(100) NOT NULL, email VARCHAR(150) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_ADMIN_EMAIL (email), PRIMARY KEY(matricule_admin)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie (ref_categorie INT AUTO_INCREMENT NOT NULL, nom_categorie VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(ref_categorie)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id_client INT AUTO_INCREMENT NOT NULL, nom_client VARCHAR(100) NOT NULL, prenom_client VARCHAR(100) NOT NULL, email VARCHAR(150) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, telephone VARCHAR(20) NOT NULL, adresse LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_CLIENT_EMAIL (email), PRIMARY KEY(id_client)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE produit (ref_produit INT AUTO_INCREMENT NOT NULL, ref_categorie INT NOT NULL, libelle_produit VARCHAR(150) NOT NULL, description LONGTEXT NOT NULL, prix NUMERIC(10, 2) NOT NULL, stock INT NOT NULL, INDEX idx_produit_ref_categorie (ref_categorie), INDEX IDX_29A5EC27F7EFEA5E (prix), PRIMARY KEY(ref_produit)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_PRODUIT_REF_CATEGORIE FOREIGN KEY (ref_categorie) REFERENCES categorie (ref_categorie) ON DELETE RESTRICT');

        $this->addSql('CREATE TABLE commande (id_commande INT AUTO_INCREMENT NOT NULL, id_client INT NOT NULL, date_commande DATE NOT NULL, total NUMERIC(10, 2) NOT NULL, statut VARCHAR(50) NOT NULL, INDEX IDX_COMMANDE_ID_CLIENT (id_client), PRIMARY KEY(id_commande)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_COMMANDE_ID_CLIENT FOREIGN KEY (id_client) REFERENCES client (id_client) ON DELETE RESTRICT');

        $this->addSql('CREATE TABLE contenir (id_commande INT NOT NULL, ref_produit INT NOT NULL, quantite INT NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, INDEX IDX_3C914DFD3E314AE8 (id_commande), INDEX IDX_3C914DFDEDB1BFF7 (ref_produit), PRIMARY KEY(id_commande, ref_produit)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contenir ADD CONSTRAINT FK_CONTENIR_ID_COMMANDE FOREIGN KEY (id_commande) REFERENCES commande (id_commande) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contenir ADD CONSTRAINT FK_CONTENIR_REF_PRODUIT FOREIGN KEY (ref_produit) REFERENCES produit (ref_produit) ON DELETE RESTRICT');

        $this->addSql('CREATE TABLE paiement (ref_paiement INT AUTO_INCREMENT NOT NULL, id_commande INT NOT NULL, mode_paiement VARCHAR(50) NOT NULL, date_paiement DATE NOT NULL, montant NUMERIC(10, 2) NOT NULL, UNIQUE INDEX UNIQ_B1DC7A1E3E314AE8 (id_commande), INDEX idx_paiement_id_commande (id_commande), PRIMARY KEY(ref_paiement)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_PAIEMENT_ID_COMMANDE FOREIGN KEY (id_commande) REFERENCES commande (id_commande) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_PAIEMENT_ID_COMMANDE');
        $this->addSql('ALTER TABLE contenir DROP FOREIGN KEY FK_CONTENIR_ID_COMMANDE');
        $this->addSql('ALTER TABLE contenir DROP FOREIGN KEY FK_CONTENIR_REF_PRODUIT');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_COMMANDE_ID_CLIENT');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_PRODUIT_REF_CATEGORIE');

        $this->addSql('DROP TABLE paiement');
        $this->addSql('DROP TABLE contenir');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE admin');
    }
}


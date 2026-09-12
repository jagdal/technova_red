<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260711113000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute url_image sur produit et remplit les URLs de démonstration';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE produit ADD url_image VARCHAR(500) DEFAULT NULL');

        $images = [
            'iPad Pro M4' => 'https://images.unsplash.com/photo-1607452258545-943d7243463c?w=800&q=85',
            'iPad Air M1' => 'https://images.unsplash.com/photo-1648806030599-c963fd14a22f?w=800&q=85',
            'iPad Pro (2022, M2 series)' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=800&q=85',
            'iPhone 12 Pro Max' => 'https://images.unsplash.com/photo-1606061587005-c1c57d134082?w=800&q=85',
            'iPhone 13 Pro Max' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&q=85',
            'iPhone 14 Pro Max' => 'https://images.unsplash.com/photo-1724051017997-15c226434b57?w=800&q=85',
            'iPhone 15 Pro Max' => 'https://images.unsplash.com/photo-1695822822491-d92cee704368?w=800&q=85',
            'iPhone 16 Pro Max' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&q=85',
            'iPhone 17 Pro Max' => 'https://images.unsplash.com/photo-1759588071781-2c3ba9128497?w=800&q=85',
            'MacBook Air (2023, M2 series)' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&q=85',
            'MacBook Pro 14' => 'https://images.unsplash.com/photo-1611186871348-b1ce06e07c0f?w=800&q=85',
            'MacBook Pro (2024, M4 series)' => 'https://images.unsplash.com/photo-1612815154858-60bb4c7ffd18?w=800&q=85',
            'MacBook Pro (M1 series)' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800&q=85',
        ];

        foreach ($images as $name => $url) {
            $this->addSql(
                'UPDATE produit SET url_image = :url WHERE libelle_produit = :name',
                ['url' => $url, 'name' => $name]
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE produit DROP url_image');
    }
}

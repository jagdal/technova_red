<?php

namespace App\Data;

/**
 * URLs d'images produit (Unsplash — pas de hotlink bloqué).
 */
final class ProductImages
{
    public const FALLBACK =
        'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800&q=85';

    /** @var array<string, string> */
    private const BY_NAME = [
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

    /** @var array<string, string> */
    private const BY_CATEGORY = [
        'iPhone' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&q=85',
        'iPad' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=800&q=85',
        'MacBook' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&q=85',
    ];

    public static function resolve(?string $urlImage, string $libelle, string $categorie = ''): string
    {
        if ($urlImage !== null && $urlImage !== '') {
            return $urlImage;
        }

        return self::BY_NAME[$libelle]
            ?? self::BY_CATEGORY[$categorie]
            ?? self::FALLBACK;
    }

    public static function forProduct(string $libelle, string $categorie): string
    {
        return self::resolve(null, $libelle, $categorie);
    }
}

<?php

namespace App\Service;

/**
 * Génère des slugs SEO à partir de libellés produits.
 */
class SlugService
{
    /**
     * Transforme un libellé en slug URL-friendly.
     */
    public function slugify(string $value): string
    {
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $slug = strtolower((string) $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        $slug = trim($slug, '-');

        return $slug;
    }
}

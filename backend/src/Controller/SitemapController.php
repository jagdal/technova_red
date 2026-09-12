<?php

namespace App\Controller;

use App\Service\ProductSearchService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Génération du sitemap XML pour le SEO.
 */
class SitemapController extends ApiController
{
    public function __construct(
        private readonly ProductSearchService $productSearchService,
    ) {
    }

    /**
     * Retourne un sitemap XML des produits.
     */
    #[Route('/api/sitemap.xml', name: 'api_sitemap', methods: ['GET'])]
    public function sitemap(): Response
    {
        $entries = $this->productSearchService->getSitemapEntries();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($entries as $entry) {
            $xml .= '<url>';
            $xml .= '<loc>https://technova.example/produit/'.htmlspecialchars($entry['slug'], ENT_XML1).'</loc>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return new Response($xml, Response::HTTP_OK, ['Content-Type' => 'application/xml']);
    }
}

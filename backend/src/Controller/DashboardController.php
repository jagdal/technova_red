<?php

namespace App\Controller;

use App\Service\DashboardService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Tableau de bord administrateur.
 */
#[Route('/api/admin/dashboard')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends ApiController
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {
    }

    /**
     * Vue d'ensemble des ventes, produits et commandes.
     */
    #[Route('', name: 'api_admin_dashboard', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->dashboardService->getOverview());
    }
}

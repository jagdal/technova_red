<?php

namespace App\Tests\Unit\Service;

use App\Service\CartService;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires du calcul du total du panier.
 */
class CartServiceTest extends TestCase
{
  private CartService $cartService;

  protected function setUp(): void
  {
    $this->cartService = new CartService(
      $this->createMock(\Symfony\Component\HttpFoundation\RequestStack::class),
      $this->createMock(\App\Repository\ProduitRepository::class),
    );
  }

  public function testCalculateTotalWithMultipleItems(): void
  {
    $items = [
      ['prix_unitaire' => '999.99', 'quantite' => 1],
      ['prix_unitaire' => '49.50', 'quantite' => 2],
    ];

    $this->assertSame('1098.99', $this->cartService->calculateTotal($items));
  }

  public function testCalculateTotalWithEmptyCart(): void
  {
    $this->assertSame('0.00', $this->cartService->calculateTotal([]));
  }
}

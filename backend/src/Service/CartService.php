<?php

namespace App\Service;

use App\Data\ProductImages;
use App\DTO\CartItemDto;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Service de gestion du panier (stocké en session).
 */
class CartService
{
    private const SESSION_KEY = 'cart';

  public function __construct(
    private readonly RequestStack $requestStack,
    private readonly ProduitRepository $produitRepository,
  ) {
  }


  public function addOrUpdateItem(CartItemDto $dto): array
  {
    $produit = $this->produitRepository->find($dto->refProduit);
    if (!$produit instanceof Produit) {
      throw new \InvalidArgumentException('Produit introuvable.');
    }

    if ($produit->getStock() < $dto->quantite) {
      throw new \InvalidArgumentException('Stock insuffisant pour ce produit.');
    }

    $cart = $this->getCart();
    $cart[$dto->refProduit] = $dto->quantite;
    $this->saveCart($cart);

    return $this->getCartDetails();
  }

  /**
   * Supprime un produit du panier.
   *
   * @return array<string, mixed>
   */
  public function removeItem(int $refProduit): array
  {
    $cart = $this->getCart();
    unset($cart[$refProduit]);
    $this->saveCart($cart);

    return $this->getCartDetails();
  }

  /**
   * Retourne le contenu détaillé du panier.
   *
   * @return array{items: list<array<string, mixed>>, total: string}
   */
  public function getCartDetails(): array
  {
    $cart = $this->getCart();
    $items = [];

    foreach ($cart as $refProduit => $quantite) {
      $produit = $this->produitRepository->find($refProduit);
      if (!$produit instanceof Produit) {
        continue;
      }

      $prixUnitaire = $produit->getPrix();
      $items[] = [
        'ref_produit' => $produit->getRefProduit(),
        'libelle_produit' => $produit->getLibelleProduit(),
        'url_image' => ProductImages::resolve(
          $produit->getUrlImage(),
          $produit->getLibelleProduit(),
          $produit->getCategorie()->getNomCategorie(),
        ),
        'prix_unitaire' => $prixUnitaire,
        'quantite' => $quantite,
        'sous_total' => number_format((float) $prixUnitaire * $quantite, 2, '.', ''),
      ];
    }

    return [
      'items' => $items,
      'total' => $this->calculateTotal($items),
    ];
  }

  /**
   * Calcule le total du panier à partir des lignes.
   *
   * @param list<array{prix_unitaire: string, quantite: int}> $items
   */
  public function calculateTotal(array $items): string
  {
    $total = 0.0;

    foreach ($items as $item) {
      $total += (float) $item['prix_unitaire'] * $item['quantite'];
    }

    return number_format($total, 2, '.', '');
  }

  /**
   * Vide le panier après validation de commande.
   */
  public function clear(): void
  {
    $session = $this->requestStack->getSession();
    $session->remove(self::SESSION_KEY);
  }

  /**
   * @return array<int, int> ref_produit => quantite
   */
  public function getCart(): array
  {
    $session = $this->requestStack->getSession();

    /** @var array<int, int> $cart */
    $cart = $session->get(self::SESSION_KEY, []);

    return $cart;
  }

  /**
   * @param array<int, int> $cart
   */
  private function saveCart(array $cart): void
  {
    $this->requestStack->getSession()->set(self::SESSION_KEY, $cart);
  }
}

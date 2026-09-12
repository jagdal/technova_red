<?php

namespace App\Tests\Functional;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Enum\CommandeStatut;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Scénario fonctionnel : ajout au panier puis validation de commande.
 */
class CheckoutFlowTest extends WebTestCase
{
  public function testClientCanCheckoutOrderFromCart(): void
  {
    $client = static::createClient();
    $entityManager = static::getContainer()->get(EntityManagerInterface::class);
    $produit = $this->createTestProduct($entityManager, '1299.99', 10);

    $client->request('GET', '/api/auth/csrf');
    $this->assertResponseIsSuccessful();
    $csrf = json_decode($client->getResponse()->getContent(), true);
    $registerToken = $csrf['register_token'];

    $email = 'client_test_'.uniqid().'@technova.test';
    $client->request(
      'POST',
      '/api/auth/register',
      server: ['CONTENT_TYPE' => 'application/json'],
      content: json_encode([
        'nomClient' => 'Martin',
        'prenomClient' => 'Lucas',
        'email' => $email,
        'motDePasse' => 'password123',
        'telephone' => '0612345678',
        'adresse' => '10 avenue des Champs',
        'csrfToken' => $registerToken,
      ], JSON_THROW_ON_ERROR)
    );
    $this->assertResponseStatusCodeSame(201);
    $auth = json_decode($client->getResponse()->getContent(), true);
    $token = $auth['token'];

    $client->setServerParameter('HTTP_Authorization', 'Bearer '.$token);
    $client->request(
      'POST',
      '/api/panier/items',
      server: ['CONTENT_TYPE' => 'application/json'],
      content: json_encode([
        'refProduit' => $produit->getRefProduit(),
        'quantite' => 2,
      ], JSON_THROW_ON_ERROR)
    );
    $this->assertResponseIsSuccessful();

    $client->request('POST', '/api/commandes/checkout');
    $this->assertResponseStatusCodeSame(201);

    $response = json_decode($client->getResponse()->getContent(), true);
    $this->assertSame('2599.98', $response['total']);
    $this->assertSame(CommandeStatut::EN_ATTENTE->value, $response['statut']);

    /** @var CommandeRepository $commandeRepository */
    $commandeRepository = static::getContainer()->get(CommandeRepository::class);
    $commande = $commandeRepository->find($response['id_commande']);

    $this->assertNotNull($commande);
    $this->assertSame(CommandeStatut::EN_ATTENTE->value, $commande->getStatut());
    $this->assertSame('2599.98', $commande->getTotal());
  }

  private function createTestProduct(EntityManagerInterface $entityManager, string $prix, int $stock): Produit
  {
    $categorie = (new Categorie())
      ->setNomCategorie('iPhone')
      ->setDescription('Smartphones Apple');

    $produit = (new Produit())
      ->setLibelleProduit('iPhone 15 Pro')
      ->setDescription('Smartphone Apple haut de gamme')
      ->setPrix($prix)
      ->setStock($stock)
      ->setCategorie($categorie);

    $entityManager->persist($categorie);
    $entityManager->persist($produit);
    $entityManager->flush();

    return $produit;
  }
}

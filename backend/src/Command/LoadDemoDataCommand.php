<?php

namespace App\Command;

use App\Data\ProductImages;
use App\Entity\Admin;
use App\Entity\Categorie;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Charge les catégories, produits Apple et un compte admin de démonstration.
 */
#[AsCommand(
    name: 'app:load-demo-data',
    description: 'Charge les données de démonstration TechNova (catégories, produits, admin)',
)]
class LoadDemoDataCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->entityManager->getRepository(Produit::class)->findAll() as $p) {
            $this->entityManager->remove($p);
        }
        foreach ($this->entityManager->getRepository(Categorie::class)->findAll() as $c) {
            $this->entityManager->remove($c);
        }
        $this->entityManager->flush();

        $categoriesData = [
            'iPhone' => 'Smartphones Apple — iPhone 12 à 17 Pro Max.',
            'iPad' => 'Tablettes Apple — iPad Air et iPad Pro.',
            'MacBook' => 'Ordinateurs portables Apple — MacBook Air et Pro.',
        ];

        $categories = [];
        foreach ($categoriesData as $name => $description) {
            $cat = (new Categorie())->setNomCategorie($name)->setDescription($description);
            $this->entityManager->persist($cat);
            $categories[$name] = $cat;
        }

        $productsData = [
            ['iPad Pro M4', 'iPad', '1299.00', 15, 'Ultra Retina XDR display, M4 chip, Pro cameras.'],
            ['iPad Air M1', 'iPad', '599.00', 20, 'Liquid Retina display, M2 chip, all-day battery.'],
            ['iPad Pro (2022, M2 series)', 'iPad', '349.00', 12, '10.9" Liquid Retina, A14 chip, 12MP cameras.'],
            ['iPhone 12 Pro Max', 'iPhone', '899.00', 25, '6.7" Super Retina XDR, A14 Bionic, Pro camera system.'],
            ['iPhone 13 Pro Max', 'iPhone', '999.00', 22, '6.7" ProMotion display, A15 Bionic, Cinematic mode.'],
            ['iPhone 14 Pro Max', 'iPhone', '1099.00', 18, 'Dynamic Island, 48MP Main, A16 Bionic.'],
            ['iPhone 15 Pro Max', 'iPhone', '1199.00', 16, 'Titanium design, A17 Pro, 5x optical zoom.'],
            ['iPhone 16 Pro Max', 'iPhone', '1299.00', 14, 'A18 Pro chip, 48MP cameras, Capture button.'],
            ['iPhone 17 Pro Max', 'iPhone', '1399.00', 10, 'Next-gen chip, advanced AI camera.'],
            ['MacBook Air (2023, M2 series)', 'MacBook', '1199.00', 8, 'Thin, light, powerful. All-day battery.'],
            ['MacBook Pro 14', 'MacBook', '1999.00', 6, 'M4 Pro chip, Liquid Retina XDR.'],
            ['MacBook Pro (2024, M4 series)', 'MacBook', '2499.00', 5, 'M4 Max chip, expansive 16" display.'],
            ['MacBook Pro (M1 series)', 'MacBook', '900.00', 9, 'Apple M1, 16Go RAM, 1000 Go SSD.'],
        ];

        foreach ($productsData as [$name, $catName, $price, $stock, $desc]) {
            $produit = (new Produit())
                ->setLibelleProduit($name)
                ->setDescription($desc)
                ->setPrix($price)
                ->setStock($stock)
                ->setUrlImage(ProductImages::forProduct($name, $catName))
                ->setCategorie($categories[$catName]);

            $this->entityManager->persist($produit);
        }

        $adminRepo = $this->entityManager->getRepository(Admin::class);
        $admin = $adminRepo->findOneBy(['email' => 'admin@technova.com']);
        if (!$admin instanceof Admin) {
            $admin = (new Admin())
                ->setNomAdmin('Administrateur')
                ->setEmail('admin@technova.com');
            $admin->setMotDePasse($this->passwordHasher->hashPassword($admin, 'Admin1234!'));
            $this->entityManager->persist($admin);
        }

        $this->entityManager->flush();

        $io->success(sprintf(
            'Données chargées : %d catégories, %d produits. Admin : admin@technova.com / Admin1234!',
            count($categories),
            count($productsData)
        ));

        return Command::SUCCESS;
    }
}

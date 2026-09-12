<?php

namespace App\Tests\Unit\DTO;

use App\DTO\RegisterClientDto;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Tests de validation du DTO d'inscription client.
 */
class RegisterClientDtoValidationTest extends KernelTestCase
{
  private ValidatorInterface $validator;

  protected function setUp(): void
  {
    self::bootKernel();
    $this->validator = static::getContainer()->get(ValidatorInterface::class);
  }

  public function testInvalidEmailIsRejected(): void
  {
    $dto = new RegisterClientDto();
    $dto->nomClient = 'Dupont';
    $dto->prenomClient = 'Jean';
    $dto->email = 'email-invalide';
    $dto->motDePasse = 'motdepasse123';
    $dto->telephone = '0600000000';
    $dto->adresse = '1 rue de Paris';
    $dto->csrfToken = 'token';

    $violations = $this->validator->validate($dto);

    $this->assertGreaterThan(0, count($violations));
    $this->assertSame('email', $violations->get(0)->getPropertyPath());
    $this->assertStringContainsString('email', strtolower($violations->get(0)->getMessage()));
  }
}

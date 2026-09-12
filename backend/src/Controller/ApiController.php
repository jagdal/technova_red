<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Méthodes utilitaires partagées par les contrôleurs API.
 */
abstract class ApiController extends AbstractController
{
    /**
     * Retourne une réponse JSON d'erreur de validation.
     */
    protected function validationErrorResponse(ConstraintViolationListInterface $violations): JsonResponse
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return $this->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

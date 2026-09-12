<?php

namespace App\Controller;

use App\DTO\ContactMessageDto;
use App\Service\ContactService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Formulaire de contact public.
 */
#[Route('/api/contact')]
class ContactController extends ApiController
{
    public function __construct(
        private readonly ContactService $contactService,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('', name: 'api_contact_submit', methods: ['POST'])]
    public function submit(Request $request): JsonResponse
    {
        /** @var ContactMessageDto $dto */
        $dto = $this->serializer->deserialize($request->getContent(), ContactMessageDto::class, 'json');
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            return $this->validationErrorResponse($violations);
        }

        $message = $this->contactService->submit($dto);

        return $this->json([
            'message' => 'Votre message a bien été envoyé. Nous vous répondrons rapidement.',
            'id' => $message->getIdMessage(),
        ], Response::HTTP_CREATED);
    }
}

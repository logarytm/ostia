<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    protected function createBadRequestJsonResponse(string $reason, string $message): JsonResponse
    {
        return new JsonResponse([
            'reason' => $reason,
            'message' => $message,
        ], 400);
    }

    protected function user(): ?User
    {
        /** @var User $user */
        $user = $this->getUser();

        return $user;
    }
}

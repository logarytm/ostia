<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    protected function user(): ?User
    {
        /** @var User $user */
        $user = $this->getUser();

        return $user;
    }
}

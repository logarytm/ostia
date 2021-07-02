<?php

declare(strict_types=1);

namespace App\Controller\Ajax;

use App\Controller\AbstractController;
use App\Entity\User;
use App\Query\GetPlaylist;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PlaylistController extends AbstractController
{
    /**
     * @Route("/ajax/playlists/{id}")
     */
    public function index(UuidInterface $id, User $user, GetPlaylist $getPlaylist): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->json($getPlaylist($user, $id));
    }
}
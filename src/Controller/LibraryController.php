<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\TrackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class LibraryController extends AbstractController
{
    private TrackRepository $tracks;

    public function __construct(TrackRepository $tracks)
    {
        $this->tracks = $tracks;
    }

    /**
     * @Route("/library/tracks")
     */
    public function tracks(): Response
    {
        $tracks = $this->tracks->all();

        return $this->render('library/tracks.html.twig', [
            'tracks' => $tracks
        ]);
    }
}

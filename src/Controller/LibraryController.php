<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\TrackRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
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
        $tracks = $this->tracks->all($this->user());

        return $this->render('library/tracks.html.twig', [
            'tracks' => $tracks
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\TrackRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
     * @Route("/tracks")
     */
    public function tracks(SerializerInterface $serializer): Response
    {
        $tracks = $this->tracks->all($this->user());

        return $this->render('library/tracks.html.twig', [
            'tracks' => $tracks,
            'tracks_json' => $serializer->serialize($tracks, 'json'),
        ]);
    }
}

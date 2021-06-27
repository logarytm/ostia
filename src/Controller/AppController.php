<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\TrackRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class AppController extends AbstractController
{
    private TrackRepository $tracks;

    public function __construct(TrackRepository $tracks)
    {
        $this->tracks = $tracks;
    }

    /**
     * @Route("/app")
     */
    public function index(SerializerInterface $serializer): Response
    {
        $tracks = $this->tracks->all($this->user());

        return $this->render('app.html.twig', [
            'tracks_json' => $serializer->serialize($tracks, 'json'),
        ]);
    }
}

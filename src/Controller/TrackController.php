<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TrackController extends AbstractController
{
    /**
     * @Route("/tracks/add", methods={"GET"})
     */
    public function add(): Response
    {
        return $this->render('track/add.html.twig');
    }

    /**
     * @Route("/tracks/upload", methods={"POST"})
     */
    public function upload(Request $request): Response
    {
        dd($request->files);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AppController extends AbstractController
{
    /**
     * @Route("/app")
     */
    public function index(): Response
    {
        return $this->render('app.html.twig');
    }
}

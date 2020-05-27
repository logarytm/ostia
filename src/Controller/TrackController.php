<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\TrackFile;
use App\Repository\TrackRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TrackController extends AbstractController
{
    private $tracks;

    public function __construct(TrackRepository $tracks)
    {
        $this->tracks = $tracks;
    }

    /**
     * @Route("/tracks/add", methods={"GET"})
     */
    public function add(): Response
    {
        return $this->render('track/add.html.twig');
    }

    /**
     * @Route("/tracks/ajaxUpload", methods={"POST"})
     */
    public function ajaxUpload(Request $request): Response
    {
        $uuid = Uuid::uuid4();
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('file');

        if (!$uploadedFile) {
            return $this->badRequest('file_not_uploaded', 'File not uploaded');
        }

        if (is_array($uploadedFile)) {
            // FUCK YOU SYMFONY FOR YOUR BROKEN $_FILES PARSING LOGIC
            $uploadedFile = new UploadedFile(
                $uploadedFile['tmp_name'],
                $uploadedFile['name'],
                $uploadedFile['type'],
                $uploadedFile['error']
            );
        }

        $uploadedFile->move($this->getParameter('app.temporary_dir'), $uuid->toString());

        $trackFile = new TrackFile();
        $trackFile->setName($uploadedFile->getClientOriginalName());
        $trackFile->setUuid($uuid);
        $this->tracks->add($trackFile);

        return new JsonResponse(['uuid' => $uuid->toString()], 201);
    }

    /**
     * @Route("/tracks/flow/tagging")
     */
    public function tags(Request $request): Response
    {
        $uuids = explode(',', $request->query->get('uuids'));
        $uuids = array_map('trim', $uuids);

        return $this->render('track/tags.html.twig');
    }

    private function badRequest(string $reason, string $message): JsonResponse
    {
        return new JsonResponse([
            'reason' => $reason,
            'message' => $message,
        ], 400);
    }
}

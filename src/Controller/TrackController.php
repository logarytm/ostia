<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Track;
use App\Entity\TrackFile;
use App\Repository\TrackFileRepository;
use App\Repository\TrackRepository;
use DateInterval;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TrackController extends AbstractController
{
    private TrackRepository $tracks;
    private TrackFileRepository $trackFiles;

    public function __construct(TrackRepository $tracks, TrackFileRepository $trackFiles)
    {
        $this->tracks = $tracks;
        $this->trackFiles = $trackFiles;
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
        $this->trackFiles->add($trackFile);

        return new JsonResponse(['uuid' => $uuid->toString()], 201);
    }

    /**
     * @Route("/tracks/flow/tagging")
     */
    public function tags(Request $request): Response
    {
        $uuids = $this->getUuidsFromQuery($request);

        $trackFiles = $this->trackFiles->getByUuids($uuids);

        return $this->render('track/tags.html.twig', ['track_files_json' => $this->buildTrackFileJson($trackFiles)]);
    }

    /**
     * @Route("/tracks/ajaxAddToLibrary", methods={"POST"})
     */
    public function addToLibrary(Request $request): Response
    {
        $uuid = Uuid::fromString($request->request->get('uuid'));

        $trackFile = $this->trackFiles->findOneBy(['uuid' => $uuid]);
        if (!$trackFile) {
            return $this->badRequest('not_found', 'Uploaded file not found');
        }

        $track = new Track();
        // TODO TODO TODO
        $track->setTitle(pathinfo($trackFile->getName(), PATHINFO_BASENAME));
        $track->setDuration(new DateInterval('PT23M23S'));
        $track->setUser($this->getUser());

        $this->tracks->add($track);

        (new Filesystem())->rename(
            sprintf('%s/%s', $this->getParameter('app.temporary_dir'), $uuid->toString()),
            sprintf('%s/%d', $this->getParameter('app.persistent_dir'), $track->getId())
        );

        $this->trackFiles->remove($trackFile);

        return new JsonResponse([], 201);
    }

    private function badRequest(string $reason, string $message): JsonResponse
    {
        return new JsonResponse([
            'reason' => $reason,
            'message' => $message,
        ], 400);
    }

    /** @param TrackFile[] $trackFiles */
    private function buildTrackFileJson(array $trackFiles): string
    {
        $result = [];

        foreach ($trackFiles as $trackFile) {
            $result[] = [
                'uuid' => $trackFile->getUuid()->toString(),
                'name' => $trackFile->getName(),
                'title' => $trackFile->getMetadata()->title,
                'artists' => $trackFile->getMetadata()->artists,
                'albumArtists' => $trackFile->getMetadata()->albumArtists,
                'album' => $trackFile->getMetadata()->album,
                'trackNo' => $trackFile->getMetadata()->trackNo,
                'status' => 'pending',
            ];
        }

        return json_encode($result);
    }

    private function getUuidsFromQuery(Request $request): array
    {
        $uuids = explode(',', $request->query->get('uuids'));
        $uuids = array_map('trim', $uuids);
        $uuids = array_map([Uuid::class, 'fromString'], $uuids);
        return $uuids;
    }
}

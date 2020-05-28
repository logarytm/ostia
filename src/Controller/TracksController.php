<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Track;
use App\Entity\TrackUpload;
use App\Message\PrepareTrackFile;
use App\Repository\TrackRepository;
use App\Repository\TrackUploadRepository;
use App\Track\Storage;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 */
final class TracksController extends AbstractController
{
    private TrackRepository $tracks;
    private TrackUploadRepository $trackUploads;
    private Storage $storage;

    public function __construct(TrackRepository $tracks, TrackUploadRepository $trackUploads, Storage $storage)
    {
        $this->tracks = $tracks;
        $this->trackUploads = $trackUploads;
        $this->storage = $storage;
    }

    /**
     * @Route("/tracks/upload", methods={"GET"})
     */
    public function upload(): Response
    {
        return $this->render('tracks/upload.html.twig');
    }

    /**
     * @Route("/tracks/ajaxUpload", methods={"POST"})
     */
    public function ajaxUpload(Request $request, MessageBusInterface $bus): Response
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

        $upload = new TrackUpload($uuid, $uploadedFile->getClientOriginalName());
        $this->trackUploads->add($upload);
        $bus->dispatch(new PrepareTrackFile($uuid));

        return new JsonResponse(['uuid' => $uuid->toString()], 201);
    }

    /**
     * @Route("/tracks/review")
     */
    public function review(Request $request): Response
    {
        $uuids = $this->getUuidsFromQuery($request);
        $trackUploads = $this->trackUploads->getByIds(...$uuids);

        return $this->render('track/tags.html.twig', ['track_uploads_json' => $this->buildJsonForReview($trackUploads)]);
    }

    /**
     * @Route("/tracks/ajaxAddToLibrary", methods={"POST"})
     */
    public function addToLibrary(Request $request): Response
    {
        $id = Uuid::fromString($request->request->get('uuid'));
        $upload = $this->trackUploads->getById($id);

        if (!$upload) {
            return $this->badRequest('not_found', 'Uploaded file not found');
        }

        if (!$upload->isReady()) {
            return $this->badRequest('not_ready', 'Track is not ready');
        }

        $track = new Track(
            Uuid::uuid4(),
            $this->user(),
            pathinfo($upload->getName(), PATHINFO_BASENAME),
            $upload->getDuration()
        );
        $this->tracks->add($track);
        $this->storage->saveToPersistentStorage($track, $upload);
        $this->trackUploads->remove($upload);

        return new JsonResponse([], 201);
    }

    private function badRequest(string $reason, string $message): JsonResponse
    {
        return new JsonResponse([
            'reason' => $reason,
            'message' => $message,
        ], 400);
    }

    /** @param TrackUpload[] $uploads */
    private function buildJsonForReview(array $uploads): string
    {
        $result = [];

        foreach ($uploads as $upload) {
            $result[] = [
                'uuid' => $upload->getUuid()->toString(),
                'name' => $upload->getName(),
                'title' => $upload->getMetadata()->title,
                'artists' => $upload->getMetadata()->artists,
                'albumArtists' => $upload->getMetadata()->albumArtists,
                'album' => $upload->getMetadata()->album,
                'trackNo' => $upload->getMetadata()->trackNo,
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

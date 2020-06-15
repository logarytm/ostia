<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Track;
use App\Entity\TrackUpload;
use App\Exception\UploadNotFoundException;
use App\Message\PrepareTrackFile;
use App\Repository\TrackRepository;
use App\Storage\Catalogs;
use App\Storage\Storage;
use App\Util\SystemTime;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @IsGranted("ROLE_USER")
 */
final class TracksController extends AbstractController
{
    private TrackRepository $tracks;
    private Storage $storage;
    private SerializerInterface $serializer;

    public function __construct(
        TrackRepository $tracks,
        Storage $storage,
        SerializerInterface $serializer
    ) {
        $this->tracks = $tracks;
        $this->storage = $storage;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/tracks/upload", name="tracks_upload", methods={"GET"})
     */
    public function uploadAction(): Response
    {
        return $this->render('tracks/upload.html.twig');
    }

    /**
     * @Route("/ajax/tracks/upload", name="ajax_tracks_upload", methods={"POST"})
     */
    public function ajaxUploadAction(Request $request, MessageBusInterface $bus): Response
    {
        $uuid = Uuid::uuid4();
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('file');

        if (!$uploadedFile) {
            return $this->createBadRequestJsonResponse('file_not_uploaded', 'File not uploaded');
        }

        if (is_array($uploadedFile)) {
            $uploadedFile = new UploadedFile(
                $uploadedFile['tmp_name'],
                $uploadedFile['name'],
                $uploadedFile['type'],
                $uploadedFile['error']
            );
        }

        $uploadedFile->move($this->getParameter('app.temporary_dir'), $uuid->toString());

        $upload = new TrackUpload(
            $uuid,
            $this->user(),
            $uploadedFile->getClientOriginalName(),
            $this->tracks->getEndPosition($this->user(), TrackUpload::STATUS_UPLOADED),
            SystemTime::utcNow()
        );
        $this->tracks->add($upload);
        $bus->dispatch(new PrepareTrackFile($uuid));

        return new JsonResponse(['uuid' => $uuid->toString()], 201);
    }

    /**
     * @Route("/tracks/review", name="tracks_review")
     */
    public function reviewAction(Request $request): Response
    {
        $uuids = $this->getUuidsFromQuery($request);
        $tracksToReview = $this->tracks->getTracksToReview(...$uuids);

        return $this->render('tracks/review.html.twig', [
            'tracks_to_review_json' => $this->serializer->serialize($tracksToReview, 'json'),
        ]);
    }

    /**
     * @Route("/ajax/tracks/addToLibrary", name="ajax_tracks_add_to_library", methods={"POST"})
     */
    public function addToLibraryAction(Request $request): Response
    {
        $id = Uuid::fromString($request->request->get('id'));
        $upload = $this->tracks->getTrackUploadById($id);

        if (!$upload) {
            return $this->createBadRequestJsonResponse('not_found', 'Uploaded file not found');
        }

        if (!$upload->isUploadReady()) {
            return $this->createBadRequestJsonResponse('not_ready', 'Track is not ready');
        }

        $track = new Track(
            Uuid::uuid4(),
            $this->user(),
            pathinfo($upload->getTitle(), PATHINFO_BASENAME),
            $upload->getDuration(),
            $this->tracks->getEndPosition($this->user(), Track::STATUS_REVIEWED),
            SystemTime::utcNow()
        );
        $this->tracks->add($track);
        $this->storage->saveToPersistentStorage($track, $upload);
        $this->tracks->remove($upload);

        return new JsonResponse([], 201);
    }

    /**
     * @Route("/ajax/tracks/{id}/stream", name="ajax_tracks_stream")
     */
    public function streamAction(string $id): Response
    {
        return new JsonResponse([
            'preferred' => $this->storage->getUrl(Catalogs::TRACKS, Uuid::fromString($id)),
        ]);
    }

    private function getUuidsFromQuery(Request $request): array
    {
        $uuids = explode(',', $request->query->get('uuids'));
        $uuids = array_map('trim', $uuids);
        $uuids = array_map([Uuid::class, 'fromString'], $uuids);

        return $uuids;
    }
}

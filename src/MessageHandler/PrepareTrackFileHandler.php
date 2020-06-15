<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Duration;
use App\Entity\TrackUpload;
use App\Exception\UploadNotFoundException;
use App\Message\PrepareTrackFile;
use App\Repository\TrackRepository;
use App\Storage\Storage;
use FFMpeg\FFProbe;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PrepareTrackFileHandler implements MessageHandlerInterface
{
    private TrackRepository $tracks;
    private Storage $storage;
    private LoggerInterface $logger;

    public function __construct(TrackRepository $tracks, Storage $storage, LoggerInterface $logger)
    {
        $this->tracks = $tracks;
        $this->storage = $storage;
        $this->logger = $logger;
    }

    public function __invoke(PrepareTrackFile $message)
    {
        try {
            $trackFile = $this->tracks->getTrackUploadById($message->getTrackFileId());
            $this->computeDuration($trackFile);
        } catch (UploadNotFoundException $e) {
            $this->logger->warning('Track with UUID {uuid} not found.', [
                'uuid' => $message->getTrackFileId()->toString(),
            ]);
        }
    }

    private function computeDuration(TrackUpload $trackFile): void
    {
        $ffprobe = FFProbe::create();
        $totalSeconds = $ffprobe
            ->format($this->storage->getTemporaryFilePath($trackFile->getId()))
            ->get('duration');

        $trackFile->setDuration(Duration::fromSeconds((int)$totalSeconds));
    }
}

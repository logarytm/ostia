<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Duration;
use App\Entity\TrackFile;
use App\Exception\TrackFileNotFoundException;
use App\Message\PrepareTrackFile;
use App\Repository\TrackFileRepository;
use App\Track\TrackAudioStorage;
use DateInterval;
use FFMpeg\FFProbe;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PrepareTrackFileHandler implements MessageHandlerInterface
{
    private TrackFileRepository $trackFiles;
    private TrackAudioStorage $storage;
    private LoggerInterface $logger;

    public function __construct(TrackFileRepository $trackFiles, TrackAudioStorage $storage, LoggerInterface $logger)
    {
        $this->trackFiles = $trackFiles;
        $this->storage = $storage;
        $this->logger = $logger;
    }

    public function __invoke(PrepareTrackFile $message)
    {
        try {
            $trackFile = $this->trackFiles->getByUuid($message->getTrackFileUuid());
            $this->computeDuration($trackFile);
        } catch (TrackFileNotFoundException $e) {
            $this->logger->warning('Track with UUID {uuid} not found.', [
                'uuid' => $message->getTrackFileUuid()->toString(),
            ]);
        }
    }

    private function computeDuration(TrackFile $trackFile): void
    {
        $ffprobe = FFProbe::create();
        $totalSeconds = $ffprobe
            ->format($this->storage->getTemporaryFilePath($trackFile->getUuid()))
            ->get('duration');

        $trackFile->setDuration(Duration::fromSeconds((int)$totalSeconds));
    }
}

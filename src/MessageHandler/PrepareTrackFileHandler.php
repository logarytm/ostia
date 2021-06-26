<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Exception\UploadNotFoundException;
use App\Message\PrepareTrackFile;
use App\Metadata\MetadataReader;
use App\Repository\TrackRepository;
use App\Service\TrackManager;
use App\Storage\Storage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PrepareTrackFileHandler implements MessageHandlerInterface
{
    private TrackRepository $tracks;
    private TrackManager $trackManager;
    private Storage $storage;
    private MetadataReader $metadataReader;
    private LoggerInterface $logger;

    public function __construct(
        TrackRepository $tracks,
        TrackManager $trackManager,
        Storage $storage,
        MetadataReader $metadataReader,
        LoggerInterface $logger
    ) {
        $this->tracks = $tracks;
        $this->trackManager = $trackManager;
        $this->storage = $storage;
        $this->metadataReader = $metadataReader;
        $this->logger = $logger;
    }

    public function __invoke(PrepareTrackFile $message)
    {
        try {
            $trackFile = $this->tracks->getById($message->getTrackFileId());

            $trackFilePath = $this->storage->getTemporaryFilePath($trackFile->getId());
            $metadata = $this->metadataReader->readMetadata($trackFilePath, $message->getOriginalFilename());

            $this->trackManager->updateMetadata($trackFile, $metadata);
        } catch (UploadNotFoundException $e) {
            $this->logger->warning('Track with UUID {uuid} not found.', [
                'uuid' => $message->getTrackFileId()->toString(),
            ]);
        }
    }
}

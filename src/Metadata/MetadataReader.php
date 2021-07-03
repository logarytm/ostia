<?php

declare(strict_types=1);

namespace App\Metadata;

use App\Entity\Duration;
use FFMpeg\FFProbe;
use FFMpeg\FFProbe\DataMapping\AbstractData;
use SplFileInfo;
use Symfony\Component\Mime\MimeTypesInterface;

class MetadataReader
{
    private const MIME_MPEG = 'audio/mpeg';

    private FFProbe $ffprobe;
    private MimeTypesInterface $mimeTypes;
    private NativeMP3DurationReader $nativeDurationReader;

    public function __construct(MimeTypesInterface $mimeTypes, NativeMP3DurationReader $nativeDurationReader)
    {
        $this->ffprobe = FFProbe::create();
        $this->mimeTypes = $mimeTypes;
        $this->nativeDurationReader = $nativeDurationReader;
    }

    public function readMetadata(string $filePath, string $originalFilename): Metadata
    {
        $metadata = $this->ffprobe->format($filePath);
        $tags = $metadata->get('tags', []);

        return new Metadata(
            $this->determineTitle($tags, $originalFilename),
            Duration::fromSeconds($this->determineDuration($filePath, $metadata)),
            $tags['genre'] ?? null,
        );
    }

    private function determineTitle(array $tags, string $originalFilename): string
    {
        if (!empty($tags['title'])) {
            return $tags['title'];
        }

        $fileInfo = new SplFileInfo($originalFilename);

        return $fileInfo->getBasename('.' . $fileInfo->getExtension());
    }

    private function determineDuration(string $filePath, AbstractData $metadata): int
    {
        if ($metadata->get('duration') !== null) {
            return (int) $metadata->get('duration');
        }

        $mimeType = $this->mimeTypes->guessMimeType($filePath);
        if ($mimeType === self::MIME_MPEG) {
            return $this->nativeDurationReader->getDuration($filePath);
        }

        return 0;
    }
}

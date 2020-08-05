<?php

declare(strict_types=1);

namespace App\Metadata;

use App\Entity\Duration;
use FFMpeg\FFProbe;
use SplFileInfo;

class MetadataReader
{
    private FFProbe $ffprobe;

    public function __construct()
    {
        $this->ffprobe = FFProbe::create();
    }

    public function readMetadata(string $filePath, string $originalFilename): Metadata
    {
        $metadata = $this->ffprobe->format($filePath);
        $tags = $metadata->get('tags', []);

        return new Metadata(
            $this->determineTitle($tags, $originalFilename),
            Duration::fromSeconds((int) $metadata->get('duration')),
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
}

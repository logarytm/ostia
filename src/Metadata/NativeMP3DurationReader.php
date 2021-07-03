<?php

declare(strict_types=1);

namespace App\Metadata;

/**
 * This class checks duration of an MP3 file using native PHP implementation.
 * Since it reads the whole file (in order to support Variable Bit Rate) we use
 * it only as fallback when FFProbe is unable to detremine the duration on its own.
 *
 * @copyright Based on the code from http://www.zedwood.com/article/php-calculate-duration-of-mp3
 */
class NativeMP3DurationReader
{
    private const MPEG_VERSIONS = [
        0x0 => '2.5',
        0x1 => 'x',
        0x2 => '2',
        0x3 => '1',
    ];
    private const MPEG_LAYERS = [
        0x0 => 'x',
        0x1 => '3',
        0x2 => '2',
        0x3 => '1',
    ];
    private const BITRATES = [
        'V1L1' => [0, 32, 64, 96, 128, 160, 192, 224, 256, 288, 320, 352, 384, 416, 448],
        'V1L2' => [0, 32, 48, 56,  64,  80,  96, 112, 128, 160, 192, 224, 256, 320, 384],
        'V1L3' => [0, 32, 40, 48,  56,  64,  80,  96, 112, 128, 160, 192, 224, 256, 320],
        'V2L1' => [0, 32, 48, 56,  64,  80,  96, 112, 128, 144, 160, 176, 192, 224, 256],
        'V2L2' => [0,  8, 16, 24,  32,  40,  48,  56,  64,  80,  96, 112, 128, 144, 160],
        'V2L3' => [0,  8, 16, 24,  32,  40,  48,  56,  64,  80,  96, 112, 128, 144, 160],
    ];
    private const SAMPLE_RATES = [
        '1'   => [44100, 48000, 32000],
        '2'   => [22050, 24000, 16000],
        '2.5' => [11025, 12000, 8000],
    ];
    private const SAMPLES = [
        // MPEGv1
        1 => [
            1 => 384,
            2 => 1152,
            3 => 1152,
        ],
        // MPEGv2/2.5
        2 => [
            1 => 384,
            2 => 1152,
            3 => 576,
        ],
    ];

    public function getDuration(string $filePath): int
    {
        $duration = 0;

        $fd = fopen($filePath, 'rb');

        $block = fread($fd, 100);
        $offset = $this->calculateID3v2TagOffset($block);
        fseek($fd, $offset, SEEK_SET);

        while (feof($fd) === false) {
            $block = fread($fd, 10);

            if (strlen($block) < 10) {
                break;
            } elseif ($block[0] === "\xff" && (ord($block[1]) & 0xe0)) {
                // Looking for 1111 1111 111 (frame synchronization bits)
                $info = $this->parseFrameHeader(substr($block, 0, 4));
                if (empty($info['Framesize'])) {
                    // Some MP3 files are corrupted
                    return $duration;
                }

                fseek($fd, $info['Framesize'] - 10, SEEK_CUR);
                $duration += ($info['Samples'] / $info['Sampling Rate']);
            } elseif (substr($block, 0, 3) === 'TAG') {
                // Skip over id3v1 tag size
                fseek($fd, 128 - 10, SEEK_CUR);
            } else {
                fseek($fd, -9, SEEK_CUR);
            }
        }

        return (int) round($duration);
    }

    private function calculateID3v2TagOffset($block): int
    {
        if (substr($block, 0,3) !== 'ID3') {
            return 0;
        }

        $id3v2Flags = ord($block[5]);
        $isFlagFooterPresent = (bool) ($id3v2Flags & 0x10);

        $z0 = ord($block[6]);
        $z1 = ord($block[7]);
        $z2 = ord($block[8]);
        $z3 = ord($block[9]);

        if ((($z0 & 0x80) === 0) && (($z1 & 0x80) === 0) && (($z2 & 0x80) === 0) && (($z3 & 0x80)=== 0)) {
            $headerSize = 10;
            $tagSize = (($z0 & 0x7f) * 2097152) + (($z1 & 0x7f) * 16384) + (($z2 & 0x7f) * 128) + ($z3 & 0x7f);
            $footerSize = $isFlagFooterPresent ? 10 : 0;

            return $headerSize + $tagSize + $footerSize;
        }

        return 0;
    }

    private function parseFrameHeader(string $fourBytes): array
    {
        $b1 = ord($fourBytes[1]);
        $b2 = ord($fourBytes[2]);

        $versionBits = ($b1 & 0x18) >> 3;
        $version = self::MPEG_VERSIONS[$versionBits];
        $simpleVersion = $version;
        if ($version === '2.5') {
            $simpleVersion = 2;
        }

        $layer_bits = ($b1 & 0x06) >> 1;
        $layer = self::MPEG_LAYERS[$layer_bits];

        $bitrateKey = sprintf('V%dL%d', $simpleVersion, $layer);
        $bitrateIndex = ($b2 & 0xf0) >> 4;
        $bitrate = self::BITRATES[$bitrateKey][$bitrateIndex] ?? 0;

        $sampleRateIndex = ($b2 & 0x0c) >> 2;
        $sampleRate = self::SAMPLE_RATES[$version][$sampleRateIndex] ?? 0;

        $paddingBit = ($b2 & 0x02) >> 1;

        $info = [];
        $info['Sampling Rate'] = $sampleRate;
        $info['Framesize'] = $this->computeFrameSize($layer, $bitrate, $sampleRate, $paddingBit);
        $info['Samples'] = self::SAMPLES[$simpleVersion][$layer];

        return $info;
    }

    private function computeFrameSize(string $layer, int $bitrate, int $sampleRate, int $paddingBit): int
    {
        if ($layer === '1') {
            return intval(((12 * $bitrate * 1000 / $sampleRate) + $paddingBit) * 4);
        }

        // Layer 2 or 3
        return intval(((144 * $bitrate*1000)/$sampleRate) + $paddingBit);
    }
}

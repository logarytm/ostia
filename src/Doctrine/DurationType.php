<?php

declare(strict_types=1);

namespace App\Doctrine;

use App\Entity\Duration;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;

final class DurationType extends Type
{
    public const DURATION = 'duration';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return Types::INTEGER;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? Duration::fromSeconds((int)$value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Duration ? $value->getTotalSeconds() : null;
    }

    public function getName(): string
    {
        return self::DURATION;
    }
}

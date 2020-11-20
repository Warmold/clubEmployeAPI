<?php


namespace App\Doctrine\Types;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\GuidType;
use Symfony\Component\Uid\Uuid;

class UuidType extends GuidType
{
    /**
     * @var string
     */
    public const NAME = 'uuid';

    /**
     * @param string|Uuid|null $value
     * @param AbstractPlatform $platform
     *
     * @return Uuid|null
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Uuid
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof Uuid) {
            return $value;
        }

        try {
            $uuid = Uuid::fromString($value);
        } catch (\InvalidArgumentException $e) {
            throw ConversionException::conversionFailed($value, static::NAME);
        }

        return $uuid;
    }

    /**
     * @param Uuid|string|null $value
     * @param AbstractPlatform $platform
     *
     * @return string|null
     *
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (
            $value instanceof Uuid
            || (
                (is_string($value)
                    || method_exists($value, '__toString'))
                && Uuid::isValid((string)$value)
            )
        ) {
            return (string)$value;
        }

        throw ConversionException::conversionFailed($value, static::NAME);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }
}
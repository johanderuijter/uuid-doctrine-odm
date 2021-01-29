<?php

namespace JDR\Uuid\Doctrine\ODM;

use Doctrine\ODM\MongoDB\Types\Type;
use InvalidArgumentException;
use JDR\Uuid\Doctrine\ODM\Exception\ConversionException;
use MongoDB\BSON\Binary;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidBinaryType extends Type
{
    /**
     * The name of the doctrine type
     */
    public const NAME = 'ramsey_uuid_binary';

    /**
     * Converts a value from its database representation to its PHP representation of this type.
     *
     * @param mixed $value The value to convert.
     *
     * @return UuidInterface
     * @throws ConversionException
     */
    public function convertToPHPValue($value): ?UuidInterface
    {
        if (null === $value) {
            return null;
        }
        if ($value instanceof UuidInterface) {
            return $value;
        }
        if ($value instanceof Binary) {
            $value = $value->getData();
        }
        try {
            $uuid = Uuid::fromBytes($value);
        } catch (InvalidArgumentException $e) {
            throw ConversionException::conversionFailed($value, self::NAME);
        }

        return $uuid;
    }

    /**
     * Converts a value from its PHP representation to its database representation of this type.
     *
     * @param mixed $value The value to convert.
     *
     * @return \MongoDB\BSON\Binary
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value): ?Binary
    {
        if (null === $value) {
            return null;
        }
        if ($value instanceof Binary) {
            return new Binary($value->getData(), Binary::TYPE_UUID);
        }
        if (is_string($value) && Uuid::isValid($value)) {
            $value = Uuid::fromString($value);
        }
        if ($value instanceof UuidInterface) {
            return new Binary($value->getBytes(), Binary::TYPE_UUID);
        }
        throw ConversionException::conversionFailed($value, self::NAME);
    }

    public function closureToPHP(): string
    {
        return sprintf(
            'if (null === $value) {
                $uuid = null;
            } elseif ($value instanceof \Ramsey\Uuid\UuidInterface) {
                $uuid = $value;
            } else {
                if ($value instanceof \MongoDB\BSON\Binary) {
                    $value = $value->getData();
                }
                try {
                    $uuid = \Ramsey\Uuid\Uuid::fromBytes($value);
                } catch (InvalidArgumentException $e) {
                    throw \JDR\Uuid\Doctrine\ODM\Exception\ConversionException::conversionFailed($value, \'%s\');
                }
            }
            $return = $uuid;',
            self::NAME
        );
    }

    public function closureToMongo(): string
    {
        return sprintf(
            'if (null === $value) {
                $mongo = null;
            } elseif ($value instanceof \MongoDB\BSON\Binary) {
                $mongo = new \MongoDB\BSON\Binary($value->getData(), %d);
            } else {
                if (is_string($value) && \Ramsey\Uuid\Uuid::isValid($value)) {
                    $value = \Ramsey\Uuid\Uuid::fromString($value);
                }
                if ($value instanceof \Ramsey\Uuid\UuidInterface) {
                    $mongo = new \MongoDB\BSON\Binary($value->getBytes(), %d);
                } else {
                    throw \JDR\Uuid\Doctrine\ODM\Exception\ConversionException::conversionFailed($value, \'%s\');
                }
            }
            $return = $mongo;',
            Binary::TYPE_UUID,
            Binary::TYPE_UUID,
            self::NAME
        );
    }
}

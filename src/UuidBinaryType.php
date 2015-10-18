<?php

namespace JDR\Uuid\Doctrine\ODM;

use Doctrine\ODM\MongoDB\Types\Type;
use InvalidArgumentException;
use MongoBinData;
use Ramsey\Uuid\Uuid;

use JDR\Uuid\Doctrine\ODM\Exception\ConversionException;

class UuidBinaryType extends Type
{
    /**
     * The name of the doctrine type
     */
    const NAME = 'ramsey_uuid_binary';

    /**
     * Converts a value from its database representation to its PHP representation of this type.
     *
     * @param mixed $value The value to convert.
     *
     * @return Uuid
     */
    public function convertToPHPValue($value)
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof Uuid) {
            return $value;
        }

        if ($value instanceof MongoBinData) {
            $value = $value->bin;
        }

        try {
            $uuid =  Uuid::fromBytes($value);
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
     * @return MongoBinData
     */
    public function convertToDatabaseValue($value)
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof MongoBinData) {
            return new MongoBinData($value->bin, MongoBinData::UUID_RFC4122);
        }

        if (is_string($value) && Uuid::isValid($value)) {
            $value = Uuid::fromString($value);
        }

        if ($value instanceof Uuid) {
            return new MongoBinData($value->getBytes(), MongoBinData::UUID_RFC4122);
        }

        throw ConversionException::conversionFailed($value, self::NAME);
    }

    public function closureToPHP()
    {
        return sprintf(
            'if (null === $value) {
                $uuid = null;
            } elseif ($value instanceof \Ramsey\Uuid\Uuid) {
                $uuid = $value;
            } else {
                if ($value instanceof \MongoBinData) {
                    $value = $value->bin;
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

    public function closureToMongo()
    {
        return sprintf(
            'if (null === $value) {
                $mongo = null;
            } elseif ($value instanceof MongoBinData) {
                $mongo = new \MongoBinData($value->bin, %d);
            } else {
                if (is_string($value) && \Ramsey\Uuid\Uuid::isValid($value)) {
                    $value = \Ramsey\Uuid\Uuid::fromString($value);
                }

                if ($value instanceof \Ramsey\Uuid\Uuid) {
                    $mongo = new MongoBinData($value->getBytes(), %d);
                } else {
                    throw \JDR\Uuid\Doctrine\ODM\Exception\ConversionException::conversionFailed($value, \'%s\');
                }
            }

            $return = $mongo;',
            MongoBinData::UUID_RFC4122,
            MongoBinData::UUID_RFC4122,
            self::NAME
        );
    }
}

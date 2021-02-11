<?php

namespace JDR\Uuid\Doctrine\ODM\Exception;

use Doctrine\Persistence\Mapping\MappingException;

/**
 * ConversionException
 */
class ConversionException extends MappingException
{
    /**
     * Thrown when a Database to Doctrine Type Conversion fails.
     *
     * @param string $value
     * @param string $toType
     *
     * @return self
     */
    public static function conversionFailed($value, $toType)
    {
        $value = (strlen($value) > 32) ? substr($value, 0, 20) . '...' : $value;

        return new self(sprintf('Could not convert database value "%s" to Doctrine Type %s', $value, $toType));
    }
}

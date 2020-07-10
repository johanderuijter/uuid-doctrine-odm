<?php

namespace JDR\Uuid\Doctrine\ODM\Test;

use Doctrine\ODM\MongoDB\Types\Type;
use JDR\Uuid\Doctrine\ODM\Exception\ConversionException;
use JDR\Uuid\Doctrine\ODM\UuidType;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UuidTypeTest extends TestCase
{
    private $type;

    public static function setUpBeforeClass(): void
    {
        Type::registerType('ramsey_uuid', UuidType::class);
    }

    protected function setUp(): void
    {
        $this->type = Type::getType('ramsey_uuid');
    }

    public function provideValidPHPToDatabaseValues(): array
    {
        $str = 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66';
        $uuid = Uuid::fromString($str);

        return [
            [$uuid, $str],
            [$str, $str],
        ];
    }

    public function provideInvalidPHPToDatabaseValues(): array
    {
        $str = 'qwerty';
        $int = 1234567890;

        return [
            [$str],
            [$int],
        ];
    }

    public function testNullToDatabaseValue(): void
    {
        $actual = $this->type->convertToDatabaseValue(null);
        static::assertNull($actual);
    }

    /**
     * @dataProvider provideValidPHPToDatabaseValues
     *
     * @param mixed $input
     * @param string $output
     */
    public function testValidPHPToDatabaseValue($input, string $output): void
    {
        $actual = $this->type->convertToDatabaseValue($input);
        static::assertSame($output, $actual);
    }

    /**
     * @dataProvider provideInvalidPHPToDatabaseValues
     *
     * @param mixed $input
     */
    public function testInvalidPHPToDatabaseValue($input): void
    {
        $this->expectException(ConversionException::class);

        $this->type->convertToDatabaseValue($input);
    }

    /**
     * @dataProvider provideValidPHPToDatabaseValues
     *
     * @param mixed $input
     * @param string $output
     */
    public function testValidClosureToDatabase($input, string $output): void
    {
        $return = null;

        call_user_func(function ($value) use (&$return) {
            eval($this->type->closureToMongo());
        }, $input);

        static::assertSame($output, $return);
    }

    /**
     * @dataProvider provideInvalidPHPToDatabaseValues
     *
     * @param $input
     */
    public function testInvalidClosureToDatabase($input): void
    {
        $this->expectException(ConversionException::class);

        $return = null;

        call_user_func(function ($value) use (&$return) {
            eval($this->type->closureToMongo());
        }, $input);
    }

    public function provideValidDatabaseToPHPValues(): array
    {
        $str = 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66';
        $uuid = Uuid::fromString($str);

        return [
            [$uuid, $str],
            [$str, $str],
        ];
    }

    public function provideInvalidDatabaseToPHPValues(): array
    {
        $str = 'qwerty';
        $int = 1234567890;

        return [
            [$str],
            [$int],
        ];
    }

    public function testNullToPHPValue(): void
    {
        $actual = $this->type->convertToPHPValue(null);
        static::assertNull($actual);
    }

    /**
     * @dataProvider provideValidDatabaseToPHPValues
     *
     * @param $input
     * @param $output
     */
    public function testValidDatabaseToPHPValue($input, $output): void
    {
        $actual = $this->type->convertToPHPValue($input);
        static::assertInstanceOf(Uuid::class, $actual);
        static::assertSame($output, $actual->toString());
    }

    /**
     * @dataProvider provideInvalidDatabaseToPHPValues
     *
     * @param $input
     */
    public function testInvalidDatabaseToPHPValue($input): void
    {
        $this->expectException(ConversionException::class);

        $this->type->convertToPHPValue($input);
    }

    /**
     * @dataProvider provideValidDatabaseToPHPValues
     *
     * @param $input
     * @param $output
     */
    public function testValidClosureToPHP($input, $output): void
    {
        call_user_func(function ($value) use (&$return) {
            eval($this->type->closureToPHP());
        }, $input);

        static::assertInstanceOf(Uuid::class, $return);
        static::assertEquals($output, $return->toString());
    }

    /**
     * @dataProvider provideInvalidDatabaseToPHPValues
     *
     * @param $input
     */
    public function testInvalidClosureToPHP($input): void
    {
        $this->expectException(ConversionException::class);

        call_user_func(function ($value) use (&$return) {
            eval($this->type->closureToPHP());
        }, $input);
    }
}

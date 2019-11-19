<?php

namespace JDR\Uuid\Doctrine\ODM\Test;

use Doctrine\ODM\MongoDB\Types\Type;
use JDR\Uuid\Doctrine\ODM\Exception\ConversionException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UuidTypeTest extends TestCase
{
    private $type;

    public static function setUpBeforeClass(): void
    {
        Type::registerType('ramsey_uuid', 'JDR\Uuid\Doctrine\ODM\UuidType');
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
        $this->assertNull($actual);
    }

    /**
     * @dataProvider provideValidPHPToDatabaseValues
     */
    public function testValidPHPToDatabaseValue($input, $output): void
    {
        $actual = $this->type->convertToDatabaseValue($input);
        $this->assertSame($output, $actual);
    }

    /**
     * @dataProvider provideInvalidPHPToDatabaseValues
     */
    public function testInvalidPHPToDatabaseValue($input): void
    {
        $this->expectException(ConversionException::class);

        $this->type->convertToDatabaseValue($input);
    }

    /**
     * @dataProvider provideValidPHPToDatabaseValues
     */
    public function testValidClosureToDatabase($input, $output): void
    {
        $return = null;

        call_user_func(function ($value) use (&$return) {
            eval($this->type->closureToMongo());
        }, $input);

        $this->assertSame($output, $return);
    }

    /**
     * @dataProvider provideInvalidPHPToDatabaseValues
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
        $this->assertNull($actual);
    }

    /**
     * @dataProvider provideValidDatabaseToPHPValues
     */
    public function testValidDatabaseToPHPValue($input, $output): void
    {
        $actual = $this->type->convertToPHPValue($input);
        $this->assertInstanceOf(Uuid::class, $actual);
        $this->assertSame($output, $actual->toString());
    }

    /**
     * @dataProvider provideInvalidDatabaseToPHPValues
     */
    public function testInvalidDatabaseToPHPValue($input): void
    {
        $this->expectException(ConversionException::class);

        $this->type->convertToPHPValue($input);
    }

    /**
     * @dataProvider provideValidDatabaseToPHPValues
     */
    public function testValidClosureToPHP($input, $output): void
    {
        call_user_func(function ($value) use (&$return) {
            eval($this->type->closureToPHP());
        }, $input);

        $this->assertInstanceOf(Uuid::class, $return);
        $this->assertEquals($output, $return->toString());
    }

    /**
     * @dataProvider provideInvalidDatabaseToPHPValues
     */
    public function testInvalidClosureToPHP($input): void
    {
        $this->expectException(ConversionException::class);

        call_user_func(function ($value) use (&$return) {
            eval($this->type->closureToPHP());
        }, $input);
    }
}

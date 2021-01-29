<?php

namespace JDR\Uuid\Doctrine\ODM\Test;

use Doctrine\ODM\MongoDB\Types\Type;
use JDR\Uuid\Doctrine\ODM\Exception\ConversionException;
use JDR\Uuid\Doctrine\ODM\UuidBinaryType;
use MongoDB\BSON\Binary;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidBinaryTypeTest extends TestCase
{
    private $type;

    public static function setUpBeforeClass(): void
    {
        Type::registerType('ramsey_uuid_binary', UuidBinaryType::class);
    }

    protected function setUp(): void
    {
        $this->type = Type::getType('ramsey_uuid_binary');
    }

    public function provideValidPHPToDatabaseValues(): array
    {
        $str = 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66';
        $uuid = Uuid::fromString($str);
        $bin = hex2bin('ff6f8cb0c57d11e19b210800200c9a66');
        $mongo = new Binary($bin, Binary::TYPE_UUID);

        return [
            [$uuid, $bin],
            [$str, $bin],
            [$mongo, $bin],
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
        $this->assertInstanceOf(Binary::class, $actual);
        $this->assertSame($output, $actual->getData());
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

        $this->assertInstanceOf(Binary::class, $return);
        $this->assertSame($output, $return->getData());
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
        $bin = hex2bin('ff6f8cb0c57d11e19b210800200c9a66');
        $mongo = new Binary($bin, Binary::TYPE_UUID);

        return [
            [$uuid, $str],
            [$bin, $str],
            [$mongo, $str],
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
        $this->assertInstanceOf(UuidInterface::class, $actual);
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

        $this->assertInstanceOf(UuidInterface::class, $return);
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

<?php

namespace JDR\Uuid\Doctrine\ODM\Test;

use Doctrine\ODM\MongoDB\Types\Type;
use MongoDB\BSON\Binary;
use Ramsey\Uuid\Uuid;

class UuidBinaryTypeTest extends \PHPUnit_Framework_TestCase
{
    private $type;

    public static function setUpBeforeClass()
    {
        Type::registerType('ramsey_uuid_binary', 'JDR\Uuid\Doctrine\ODM\UuidBinaryType');
    }

    protected function setUp()
    {
        $this->type = Type::getType('ramsey_uuid_binary');
    }

    public function provideValidPHPToDatabaseValues()
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

    public function provideInvalidPHPToDatabaseValues()
    {
        $str = 'qwerty';
        $int = 1234567890;

        return [
            [$str],
            [$int],
        ];
    }

    public function testNullToDatabaseValue()
    {
        $actual = $this->type->convertToDatabaseValue(null);
        $this->assertNull($actual);
    }

    /**
     * @dataProvider provideValidPHPToDatabaseValues
     */
    public function testValidPHPToDatabaseValue($input, $output)
    {
        $actual = $this->type->convertToDatabaseValue($input);
        $this->assertInstanceOf(Binary::class, $actual);
        $this->assertSame($output, $actual->getData());
    }

    /**
     * @dataProvider provideInvalidPHPToDatabaseValues
     * @expectedException JDR\Uuid\Doctrine\ODM\Exception\ConversionException
     */
    public function testInvalidPHPToDatabaseValue($input)
    {
        $this->type->convertToDatabaseValue($input);
    }

    /**
     * @dataProvider provideValidPHPToDatabaseValues
     */
    public function testValidClosureToDatabase($input, $output)
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
     * @expectedException JDR\Uuid\Doctrine\ODM\Exception\ConversionException
     */
    public function testInvalidClosureToDatabase($input)
    {
        $return = null;

        call_user_func(function ($value) use (&$return) {
            eval($this->type->closureToMongo());
        }, $input);
    }

    public function provideValidDatabaseToPHPValues()
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

    public function provideInvalidDatabaseToPHPValues()
    {
        $str = 'qwerty';
        $int = 1234567890;

        return [
            [$str],
            [$int],
        ];
    }

    public function testNullToPHPValue()
    {
        $actual = $this->type->convertToPHPValue(null);
        $this->assertNull($actual);
    }

    /**
     * @dataProvider provideValidDatabaseToPHPValues
     */
    public function testValidDatabaseToPHPValue($input, $output)
    {
        $actual = $this->type->convertToPHPValue($input);
        $this->assertInstanceOf(Uuid::class, $actual);
        $this->assertSame($output, $actual->toString());
    }

    /**
     * @dataProvider provideInvalidDatabaseToPHPValues
     * @expectedException JDR\Uuid\Doctrine\ODM\Exception\ConversionException
     */
    public function testInvalidDatabaseToPHPValue($input)
    {
        $this->type->convertToPHPValue($input);
    }

    /**
     * @dataProvider provideValidDatabaseToPHPValues
     */
    public function testValidClosureToPHP($input, $output)
    {
        call_user_func(function ($value) use (&$return) {
            eval($this->type->closureToPHP());
        }, $input);

        $this->assertInstanceOf(Uuid::class, $return);
        $this->assertEquals($output, $return->toString());
    }

    /**
     * @dataProvider provideInvalidDatabaseToPHPValues
     * @expectedException JDR\Uuid\Doctrine\ODM\Exception\ConversionException
     */
    public function testInvalidClosureToPHP($input)
    {
        call_user_func(function ($value) use (&$return) {
            eval($this->type->closureToPHP());
        }, $input);
    }
}

<?php

namespace JDR\Uuid\Doctrine\ODM\Test;

use Doctrine\ODM\MongoDB\Types\Type;
use Ramsey\Uuid\Uuid;

class UuidTypeTest extends \PHPUnit_Framework_TestCase
{
    private $type;

    public static function setUpBeforeClass()
    {
        Type::registerType('ramsey_uuid', 'JDR\Uuid\Doctrine\ODM\UuidType');
    }

    protected function setUp()
    {
        $this->type = Type::getType('ramsey_uuid');
    }

    public function provideValidPHPToDatabaseValues()
    {
        $str = 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66';
        $uuid = Uuid::fromString($str);

        return [
            [$uuid, $str],
            [$str, $str],
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
        $this->assertSame($output, $actual);
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

        $this->assertSame($output, $return);
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

        return [
            [$uuid, $str],
            [$str, $str],
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
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $actual);
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

        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $return);
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

<?php

use Everest\Validation\Type;
use Everest\Validation\Types\TypeString;


class TypeStringTest extends \PHPUnit\Framework\TestCase {

	public function testConstructionFromBaseType()
	{
		$this->assertInstanceOf(TypeString::CLASS, Type::String());
	}

	public function testInitialState()
	{
		$type = new TypeString;
		$this->assertEquals('string', $type->getName());
	}

	public function testExecution()
	{
		$type = new TypeString;
		// Success
		$this->assertTrue($type->execute('name', '')->isValid());
		$result = $type->execute('name', 'Foo');
		$this->assertTrue($result->isValid());
		$this->assertEmpty($result->getErrorDescription());
		// Failure
		$result = $type->execute('name', 10);
		$this->assertFalse($result->isValid());
		$this->assertNotEmpty($result->getErrorDescription());
	}

	public function testTransformWithOptionTrim()
	{
		// Trim is default option
		$type = new TypeString;
		$this->assertEquals('Foo', $type->execute('name', "\n Foo\t ")->getTransformed());
	}

	public function testTransformWithOptionUpperCase()
	{
		$type = new TypeString(TypeString::UPPER_CASE);
		$this->assertEquals('FOO', $type->execute('name', "foo")->getTransformed());
	}

	public function testTransformWithOptionLowerCase()
	{
		// Trim is default option
		$type = new TypeString(TypeString::LOWER_CASE);
		$this->assertEquals('foo', $type->execute('name', "FOO")->getTransformed());
	}
}

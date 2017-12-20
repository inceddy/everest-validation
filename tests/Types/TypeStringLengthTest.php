<?php

use Everest\Validation\Type;
use Everest\Validation\Types\TypeStringLength;


class TypeStringLengthTest extends \PHPUnit_Framework_TestCase {

	public function testConstructionFromBaseType()
	{
		$this->assertInstanceOf(TypeStringLength::CLASS, Type::Length());
	}

	public function testInitialState()
	{
		$type = new TypeStringLength;
		$this->assertEquals('string', $type->getName());
	}

	public function testExecution()
	{
		$type = new TypeStringLength(4, 10);
		// Success
		$result = $type->execute('name', 'abcde');
		$this->assertTrue($result->isValid());
		$this->assertEmpty($result->getErrorDescription());
		// Failure (min)
		$result = $type->execute('name', 'ab');
		$this->assertFalse($result->isValid());
		$this->assertNotEmpty($result->getErrorDescription());
		// Failure (max)
		$result = $type->execute('name', 'abcdefghijklmnop');
		$this->assertFalse($result->isValid());
		$this->assertNotEmpty($result->getErrorDescription());
		// Failure no string
		$this->assertFalse($type->execute('name', 10)->isValid());
	}

	public function testInvalidConstruction()
	{
		$this->expectException(\LogicException::CLASS);
		new TypeStringLength(3, 2);
	}
}

<?php
use Everest\Validation\Type;
use Everest\Validation\Types\TypeFloat;

class TypeFloatTest extends \PHPUnit_Framework_TestCase {

	public function testConstructionFromBaseType()
	{
		$this->assertInstanceOf(TypeFloat::CLASS, Type::Float());
	}

	public function testInitialState()
	{
		$type = new TypeFloat;
		$this->assertEquals('float', $type->getName());
	}

	public function testExecution()
	{
		$type = Type::Float();

		// Success
		$this->assertTrue(($result = $type->execute('name', '10.1'))->isValid());
		$this->assertEmpty($result->getErrorMessage());
		$this->assertEquals(10.1, $result->getTransformed());
		$this->assertFalse(($result = $type->execute('name', 'C'))->isValid());
		$this->assertNotEmpty($result->getErrorMessage());
	}
}

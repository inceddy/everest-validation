<?php
use Everest\Validation\Type;
use Everest\Validation\Types\TypeFloatBetween;

class TypeFloatBetweenTest extends \PHPUnit\Framework\TestCase {

	public function testConstructionFromBaseType()
	{
		$this->assertInstanceOf(TypeFloatBetween::CLASS, Type::FloatBetween());
	}

	public function testInitialState()
	{
		$type = new TypeFloatBetween;
		$this->assertEquals('float', $type->getName());
	}

	public function testExecution()
	{
		$type = Type::FloatBetween(10, 100);

		// Success
		$this->assertTrue(($result = $type->execute('name', '10.1'))->isValid());
		$this->assertEmpty($result->getErrorDescription());
		$this->assertEquals(10.1, $result->getTransformed());
		// Failure (out of range)
		$this->assertFalse(($result = $type->execute('name', 120))->isValid());
		$this->assertNotEmpty($result->getErrorDescription());
		// Failure (wrong type)
		$this->assertFalse(($result = $type->execute('name', null))->isValid());
		$this->assertNotEmpty($result->getErrorDescription());
	}
}

<?php
use Everest\Validation\Type;
use Everest\Validation\Types\TypeIntBetween;

class TypeIntBetweenTest extends \PHPUnit\Framework\TestCase {

	public function testConstructionFromBaseType()
	{
		$this->assertInstanceOf(TypeIntBetween::CLASS, Type::IntBetween());
	}

	public function testInitialState()
	{
		$type = new TypeIntBetween;
		$this->assertEquals('int', $type->getName());
	}

	public function testExecution()
	{
		$type = Type::IntBetween(10, 100);

		// Success
		$this->assertTrue(($result = $type->execute('name', '10'))->isValid());
		$this->assertEmpty($result->getErrorDescription());
		$this->assertEquals(10, $result->getTransformed());
		// Failure (out of range)
		$this->assertFalse(($result = $type->execute('name', 120))->isValid());
		$this->assertNotEmpty($result->getErrorDescription());
		// Failure (wrong type)
		$this->assertFalse(($result = $type->execute('name', null))->isValid());
		$this->assertNotEmpty($result->getErrorDescription());
	}
}

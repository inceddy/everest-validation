<?php
use Everest\Validation\Type;
use Everest\Validation\Types\TypeInt;

class TypeIntTest extends \PHPUnit\Framework\TestCase {

	public function testConstructionFromBaseType()
	{
		$this->assertInstanceOf(TypeInt::CLASS, Type::Int());
	}

	public function testInitialState()
	{
		$type = new TypeInt;
		$this->assertEquals('int', $type->getName());
	}

	public function testExecution()
	{
		$type = Type::Int();

		// Success
		$this->assertTrue(($result = $type->execute('name', '10'))->isValid());
		$this->assertEmpty($result->getErrorDescription());
		$this->assertEquals(10, $result->getTransformed());
		// Failure
		$this->assertFalse(($result = $type->execute('name', 'C'))->isValid());
		$this->assertNotEmpty($result->getErrorDescription());
		$this->assertFalse($type->execute('name', 10.1)->isValid());
	}
}

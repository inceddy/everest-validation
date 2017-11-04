<?php
use Everest\Validation\Type;
use Everest\Validation\Types\TypeAny;

class TypeAnyTest extends \PHPUnit_Framework_TestCase {

	public function testConstructionFromBaseType()
	{
		$this->assertInstanceOf(TypeAny::CLASS, Type::Any());
	}

	public function testInitialState()
	{
		$type = new TypeAny;
		$this->assertEquals('any', $type->getName());
	}

	public function testExecution()
	{
		$type = Type::Any();

		// Success
		$this->assertTrue($type->execute('name', [])->isValid());
		$this->assertTrue($type->execute('name', new stdClass)->isValid());
	}
}

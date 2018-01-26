<?php

use Everest\Validation\Type;
use Everest\Validation\Types\TypeStruct;


class TypeStructTest extends \PHPUnit\Framework\TestCase {

	public function testConstructionFromBaseType()
	{
		$this->assertInstanceOf(TypeStruct::CLASS, Type::Struct(['a' => Type::Int()]));
	}

	public function testInitialState()
	{
		$type = new TypeStruct(['a' => Type::Int()]);
		$this->assertEquals('struct', $type->getName());
	}

	public function testExecution()
	{
		$type = new TypeStruct([
			'a' => Type::Int(),
			'b' => Type::String(),
			'c' => Type::Array(Type::Int())
		]);

		// Success
		$result = $type->execute('name', [
			'a' => '10',
			'b' => 'Foo',
			'c' => [1, '2']
		]);
		$this->assertTrue($result->isValid());
		$this->assertEmpty($result->getErrorDescription());
		$this->assertEquals([
			'a' => 10,
			'b' => 'Foo',
			'c' => [1, 2]
		], $result->getTransformed());
		// Failure
		$result = $type->execute('name', [
			'a' => 'Bar',
			'b' => 10,
			'c' => ['Foo']
		]);
		$this->assertFalse($result->isValid());
		$this->assertNotEmpty($result->getErrorDescription());

		// Failure (untyped)
		$result = $type->execute('name', ['d' => null]);
		$this->assertFalse($result->isValid());
		$this->assertNotEmpty($result->getErrorDescription());
		// Failure (not array)
		$result = $type->execute('name', 10);
		$this->assertFalse($result->isValid());
		$this->assertNotEmpty($result->getErrorDescription());
	}

	public function testInvalidConstructionEmpty()
	{
		$this->expectException(\LogicException::CLASS);
		Type::Struct([]);
	}

	public function testInvalidConstructionWrongKey()
	{
		$this->expectException(\InvalidArgumentException::CLASS);
		Type::Struct([0 => Type::Int()]);
	}

	public function testInvalidConstructionWrongType()
	{
		$this->expectException(\InvalidArgumentException::CLASS);
		Type::Struct(['a' => false]);
	}
}

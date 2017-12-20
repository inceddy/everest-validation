<?php
use Everest\Validation\Type;
use Everest\Validation\Types\{
	TypeArray,
	TypeString,
	TypeInt
};


class TypeArrayTest extends \PHPUnit_Framework_TestCase {

	public function testConstructionFromBaseType()
	{
		$this->assertInstanceOf(TypeArray::CLASS, Type::Array(Type::Int()));
	}

	public function testInitialState()
	{
		$type = new TypeArray(Type::Int());
		$this->assertEquals('array', $type->getName());
	}

	public function testExecution()
	{
		$type = Type::Array(Type::String());
		// Success
		$this->assertTrue($type->execute('name', [])->isValid());

		$result = $type->execute('name', ['Foo', 'Bar']);
		$this->assertTrue($result->isValid());
		$this->assertEmpty($result->getErrorDescription());
		
		// Failure (base type)
		$result = $type->execute('name', 'foo');
		$this->assertFalse($result->isValid());
		$this->assertNotEmpty($result->getErrorDescription());

		// Failure (sub type)
		$result = $type->execute('name', [10]);
		$this->assertFalse($result->isValid());
		$this->assertNotEmpty($result->getErrorDescription());
	}

	public function testExecutionWithOptionTrim()
	{
		$type = Type::Array(Type::Int(), TypeArray::FILTER);

		$result = $type->execute('name', [
			10,
			0,
			false,
			null,
			''
		]);

		$this->assertEquals([
			10
		], $result->getTransformed());
	}
}

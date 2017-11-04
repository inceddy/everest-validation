<?php

use Everest\Validation\Type;
use Everest\Validation\Types\TypeStringRegEx;


class TypeRegExTest extends \PHPUnit_Framework_TestCase {

	public function testConstructionFromBaseType()
	{
		$this->assertInstanceOf(TypeStringRegEx::CLASS, Type::RegEx('/^(foo)(bar)$/i'));
	}

	public function testInitialState()
	{
		$type = new TypeStringRegEx('/^(foo)(bar)$/i');
		$this->assertEquals('string', $type->getName());
	}

	public function testExecution()
	{
		$type = Type::RegEx('/^(foo)(bar)$/i');

		// Success
		$result = $type->execute('name', 'FooBar'); 
		$this->assertTrue($result->isValid());
		$this->assertEmpty($result->getErrorMessage());
		$this->assertEquals('FooBar', $result->getTransformed());

		// Failure (no string)
		$result = $type->execute('name', 10);
		$this->assertFalse($result->isValid());
		$this->assertNotEmpty($result->getErrorMessage());

		// Failure (no match)
		$result = $type->execute('name', 'FooBarFoo');
		$this->assertFalse($result->isValid());
		$this->assertNotEmpty($result->getErrorMessage());
	}

	public function testExecutionWithOptionReturnMatches()
	{
		$type = Type::RegEx('/^(foo)(bar)$/i', TypeStringRegEx::RETURN_MATCHES);
		$result = $type->execute('name', 'FooBar');
		$this->assertEquals(['FooBar', 'Foo', 'Bar'], $result->getTransformed());
	}
}

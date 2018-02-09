<?php
use Everest\Validation\Types\TypeLength;
use Everest\Validation\InvalidValidationException;

class TypeLengthTest extends \PHPUnit\Framework\TestCase {

	public function testValidInput()
	{
		$value = (new TypeLength)('short', 5);
		$this->assertEquals('short', $value);
	}

	public function testInvalidInput()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypeLength)('this-is-long', 5);
	}
}

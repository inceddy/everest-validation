<?php
use Everest\Validation\Types\TypeLengthMax;
use Everest\Validation\InvalidValidationException;

class TypeLengthMaxTest extends \PHPUnit\Framework\TestCase {

	public function testValidInput()
	{
		$value = (new TypeLengthMax)('short', 10);
		$this->assertEquals('short', $value);
	}

	public function testInvalidInput()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypeLengthMax)('this-is-long', 10);
	}
}

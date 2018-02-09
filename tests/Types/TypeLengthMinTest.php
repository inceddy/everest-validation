<?php
use Everest\Validation\Types\TypeLengthMin;
use Everest\Validation\InvalidValidationException;

class TypeLengthMinTest extends \PHPUnit\Framework\TestCase {

	public function testValidInput()
	{
		$value = (new TypeLengthMin)('this-is-long', 10);
		$this->assertEquals('this-is-long', $value);
	}

	public function testInvalidInput()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypeLengthMin)('short', 10);
	}
}

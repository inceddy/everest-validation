<?php
use Everest\Validation\Types\TypeLengthBetween;
use Everest\Validation\InvalidValidationException;

class TypeLengthBetweenTest extends \PHPUnit\Framework\TestCase {

	public function testValidInput()
	{
		$value = (new TypeLengthBetween)('short', 0, 10);
		$this->assertEquals('short', $value);
	}

	public function testInvalidInput()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypeLengthBetween)('this-is-long', 0, 10);
	}
}

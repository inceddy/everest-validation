<?php
use Everest\Validation\Types\TypeBetween;
use Everest\Validation\InvalidValidationException;

class TypeBetweenTest extends \PHPUnit\Framework\TestCase {

	public function testValidInput()
	{
		$value = (new TypeBetween)(5, 0, 10);
		$this->assertSame(5, $value);
	}

	public function testInvalidInput()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypeBetween)(11, 0, 10);
	}
}

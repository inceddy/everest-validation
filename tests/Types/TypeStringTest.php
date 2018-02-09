<?php
use Everest\Validation\Types\TypeString;
use Everest\Validation\InvalidValidationException;

class TypeStringTest extends \PHPUnit\Framework\TestCase {

	public function testValidInput()
	{
		$value = (new TypeString)('String');
		$this->assertSame('String', $value);
	}

	public function testInvalidInput()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypeString)(false);
	}
}

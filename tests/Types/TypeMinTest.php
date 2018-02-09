<?php
use Everest\Validation\Types\TypeMin;
use Everest\Validation\InvalidValidationException;

class TypeMinTest extends \PHPUnit\Framework\TestCase {

	public function testValidInput()
	{
		$value = (new TypeMin)(20, 10);
		$this->assertEquals(20, $value);
	}

	public function testInvalidInput()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypeMin)(20, 30);
	}
}

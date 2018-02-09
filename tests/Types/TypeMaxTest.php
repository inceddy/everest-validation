<?php
use Everest\Validation\Types\TypeMax;
use Everest\Validation\InvalidValidationException;

class TypeMaxTest extends \PHPUnit\Framework\TestCase {

	public function testValidInput()
	{
		$value = (new TypeMax)(20, 30);
		$this->assertEquals(20, $value);
	}

	public function testInvalidInput()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypeMax)(20, 15);
	}
}

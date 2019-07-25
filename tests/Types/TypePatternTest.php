<?php
use Everest\Validation\Types\TypePattern;
use Everest\Validation\InvalidValidationException;

class TypePatternTest extends \PHPUnit\Framework\TestCase {

	public function testValidInput()
	{
		$value = (new TypePattern)('FoO', '/^foo$/i');
		$this->assertSame('FoO', $value);
	}

	public function testInvalidInput()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypePattern)('FoO2', '/^foo$/i');
	}
}

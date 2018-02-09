<?php
use Everest\Validation\Types\TypeEnum;
use Everest\Validation\InvalidValidationException;

class TypeEnumTest extends \PHPUnit\Framework\TestCase {

	public function testValidInput()
	{
		$value = (new TypeEnum)('in', ['in', 'out']);
		$this->assertSame('in', $value);
	}

	public function testValidIntputWithTransformation()
	{
		$value = (new TypeEnum)('in', ['in' => true, 'out' => false]);
		$this->assertSame(true, $value);
	}

	public function testInvalidInput()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypeEnum)('yes', ['in', 'out']);
	}
}

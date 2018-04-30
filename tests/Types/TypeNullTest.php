<?php
use Everest\Validation\Types\TypeNull;
use Everest\Validation\InvalidValidationException;

class TypeNullTest extends \PHPUnit\Framework\TestCase {

	public function validInputProvider()
	{
		return [
			[null, null]
		];
	}

	public function invalidInputProvider()
	{
		return [
			[' '],
			['5.1a'],
			[false]
		];
	}

	/**
	 * @dataProvider validInputProvider
	 */
	
	public function testValidInput($input, $expected)
	{
		$value = (new TypeNull)($input);
		$this->assertSame($expected, $value);
	}

	/**
	 * @dataProvider invalidInputProvider
	 */

	public function testInvalidInput($input)
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypeNull)($input);
	}
}

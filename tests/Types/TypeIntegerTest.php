<?php
use Everest\Validation\Types\TypeInteger;
use Everest\Validation\InvalidValidationException;

class TypeIntegerTest extends \PHPUnit\Framework\TestCase {

	public function validInputProvider()
	{
		return [
			[  5, 5],
			['5', 5],
		];
	}

	public function invalidInputProvider()
	{
		return [
			[' '],
			['5.1'],
			[false]
		];
	}

	/**
	 * @dataProvider validInputProvider
	 */
	
	public function testValidInput($input, $expected)
	{
		$value = (new TypeInteger)($input);
		$this->assertEquals($expected, $value);
	}

	/**
	 * @dataProvider invalidInputProvider
	 */

	public function testInvalidInput($input)
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypeInteger)($input);
	}
}

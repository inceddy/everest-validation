<?php
use Everest\Validation\Types\TypeBoolean;
use Everest\Validation\InvalidValidationException;

class TypeBooleanTest extends \PHPUnit\Framework\TestCase {

	public function validTrueInputProvider()
	{
		return [['true'], [true], ['1'], [1]];
	}

	public function validFalseInputProvider()
	{
		return [['false'], [false], ['0'], [0]];
	}

	public function invalidInputProvider()
	{
		return [[20], ['some-string'], [null], [[]]];
	}

	/**
	 * @dataProvider validTrueInputProvider
	 */
	
	public function testValidTrueInput($input)
	{
		$value = (new TypeBoolean)($input);
		$this->assertSame(true, $value);
	}

	/**
	 * @dataProvider validFalseInputProvider
	 */
	
	public function testValidFalseInput($input)
	{
		$value = (new TypeBoolean)($input);
		$this->assertSame(false, $value);
	}

	/**
	 * @dataProvider invalidInputProvider
	 */

	public function testInvalidInput($input)
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypeBoolean)($input);
	}
}

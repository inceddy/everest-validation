<?php
use Everest\Validation\Types\TypeNotEmpty;
use Everest\Validation\Validation;
use Everest\Validation\InvalidValidationException;

class TypeNotEmptyTest extends \PHPUnit\Framework\TestCase {

	public function notEmptyDataProvider()
	{
		return [
			['not-empty'],
			[[1]],
			[1],
			[true]
		];
	}

	public function emptyDataProvider()
	{
		return [
			[''],
			[null],
			[[]],
			[0],
			[false]
		];
	}

	/**
	 * @dataProvider notEmptyDataProvider
	 */
	
	public function testValidInput($value)
	{
		$trans = (new TypeNotEmpty)($value);
		$this->assertEquals($value, $trans);
	}

	/**
	 * @dataProvider emptyDataProvider
	 */

	public function testInvalidInputEmptyString($value)
	{
		$this->expectException(InvalidValidationException::CLASS);
		Validation::notEmpty($value);
	}
}

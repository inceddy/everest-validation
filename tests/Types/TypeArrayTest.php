<?php
use Everest\Validation\Types\TypeArray;
use Everest\Validation\InvalidValidationException;

class TypeArrayTest extends \PHPUnit\Framework\TestCase {

	public function testValidInput()
	{
		$value = (new TypeArray)([1, 2, 'key' => 3]);
		$this->assertEquals([1, 2, 'key' => 3], $value);
	}

	public function testInvalidInput()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypeArray)('no-array');
	}
}

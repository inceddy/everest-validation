<?php
use Everest\Validation\Types\TypeKeyExists;
use Everest\Validation\InvalidValidationException;

class TypeKeyExistsTest extends \PHPUnit\Framework\TestCase {

	public function testValidInput()
	{
		$value = (new TypeKeyExists)(['key' => true], 'key');
		$this->assertEquals(['key' => true], $value);
	}

	public function testInvalidArrayInput()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypeKeyExists)('no-array', 'key');
	}

	public function testInvalidKeyInput()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypeKeyExists)(['key' => true], 'not-key');
	}
}

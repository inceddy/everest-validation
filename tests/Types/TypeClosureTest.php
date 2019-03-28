<?php
use Everest\Validation\Types\TypeClosure;
use Everest\Validation\Validation;
use Everest\Validation\InvalidValidationException;

class TypeClosureTest extends \PHPUnit\Framework\TestCase {

	public function testValidInput()
	{
		$value = (new TypeClosure)(
			'value',
			'message',
			'key',
			function($value, $message, $key) {
				$this->assertSame('message', $message);
				$this->assertSame('key', $key);
				return $value;
			}
		);

		$this->assertSame('value', $value);
	}

	public function testInvalidInput()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypeClosure)(
			'value',
			'message',
			'key',
			function($value, $message, $key) {
				throw new InvalidValidationException('some_name', $message, $key, $value);
			}
		);

		$this->assertSame('value', $value);
	}

	public function testValidationShorthand()
	{
		$this->assertTrue(
			Validation::closure('value', 'message', 'key', function($value, $message, $key) {
				return true;
			})
		);
	}
}

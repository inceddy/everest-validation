<?php

use Everest\Validation\Validator;
use Everest\Validation\Type;
use Everest\Validation\Types\{
	TypeArray,
	TypeInt
};


/**
 * @author  Philipp Steingrebe <philipp@steingrebe.de>
 */

class ValidatorTest extends \PHPUnit_Framework_TestCase {

	public function testValidatorSuccess()
	{
		$values = [
			'string' => 'foo', 
			'int' => '30', 
			'array' => ['foo', 'bar']
		];

		$result = (new Validator)
			->validate('string', Type::String())
			->validate('int', Type::Int())
			->validate('array', Type::Array(Type::String()))
			->validate('unset', 'default')
			->execute($values);

		$this->assertTrue($result->isValid());
		$this->assertEquals([
			'string' => 'foo', 
			'int' => 30, 
			'array' => ['foo', 'bar'],
			'unset' => 'default'
		], $result->values());

		$this->assertEmpty($result->errors());
	}

	public function testValidatorFailure()
	{
		$store = [
			'string' => 12, 
			'int' => 'Foo', 
			'array' => true
		];

		$result = ($v = new Validator)
			->validate('string', Type::String())
			->validate('int', Type::Int())
			->validate('array', Type::Array(Type::String()))
			->validate('unset', 'default')
			->execute($store);

		$this->assertFalse($result->isValid());
		$this->assertEquals([
			'unset' => 'default'
		], $result->values());

		$this->assertNotEmpty($result->errors());
	}

	public function testValidationOnDefaultValue()
	{
		$values = [];

		$result = (new Validator)
			->validate('unset', false, Type::String())
			->execute($values);

		$this->assertFalse($result->isValid());
		$this->assertNotEmpty($result->errors());
	}

	public function testExecutionFromArray()
	{
		$this->assertTrue((new Validator)->execute([])->isValid());
	}

	public function testExecutionFromObject()
	{
		$store = new class {
			public function toArray() {
				return [];
			}
		};

		$this->assertTrue((new Validator)->execute($store)->isValid());
	}

	public function testExecutionFailureFromInvalidType()
	{
		$this->expectException(\InvalidArgumentException::CLASS);
		(new Validator)->execute(false);
	}

	public function testExecutionFailureFromInvalidObject()
	{
		$invalidStore = new class {};

		$this->expectException(\InvalidArgumentException::CLASS);
		(new Validator)->execute($invalidStore);
	}
}

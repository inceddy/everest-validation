<?php

use Everest\Validation\Validate;
use Everest\Validation\Validation;
use Everest\Validation\InvalidLazyValidationException;
use Everest\Validation\InvalidValidationException;


/**
 * @author  Philipp Steingrebe <philipp@steingrebe.de>
 */

class ValidateTest extends \PHPUnit\Framework\TestCase {

	public function testValidLazyValidation()
	{
		$data = [
			'date'  => '2018.02.08',
			'enum'  => 'ja'
		];

		$transformed = Validate::lazy($data)
			->that('date')->string()->dateTime('Y.m.d')
			->that('unset')->optional()->integer()->between(10, 20)
			->that('enum')->enum(['ja' => true, 'nein' => false])
			->execute();

		$this->assertInstanceOf(\DateTime::CLASS, $transformed['date']);
		$this->assertFalse(isset($transformed['unset']));
		$this->assertTrue($transformed['enum']);
	}


	public function testInvalidLazyValidation()
	{
		$this->expectException(InvalidLazyValidationException::CLASS);
		$this->expectExceptionMessage('The following');

		$data = [
			'date'  => '2018.02.08',
			'enum'  => 'yes'
		];

		$transformed = Validate::lazy($data)
			->that('date')->string()->dateTime('Y-d-m')
			->that('unset')->integer()->between(10, 20)
			->that('enum')->enum(['ja' => true, 'nein' => false])
			->execute();
	}

	public function testValidStrictValidation()
	{
		$data = [
			'date'  => '2018.02.08',
			'enum'  => 'ja',
			'any'   => true,
			'none'  => ''
		];

		$transformed = Validate::lazy($data)
			->that('date')->string()->dateTime('Y.m.d')
			->that('unset')->optional()->integer()->between(10, 20)
			->that('enum')->enum(['ja' => true, 'nein' => false])
			->that('any')
			->execute();


		$this->assertInstanceOf(\DateTime::CLASS, $transformed['date']);
		$this->assertFalse(isset($transformed['unset']));
		$this->assertTrue($transformed['enum']);
		$this->assertTrue($transformed['any']);
		$this->assertFalse(isset($transformed['none']));
	}


	public function testInvalidStrictValidation()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$this->expectExceptionMessage('2018.02.08');

		$data = [
			'date'  => '2018.02.08',
			'unset' => null,
			'enum'  => 'yes'
		];

		$transformed = Validate::strict($data)
			->that('date')->string()->dateTime('Y-m-d')
			->that('unset')->integer()->between(10, 20)
			->that('enum')->enum(['ja' => true, 'nein' => false])
			->execute();
	}

	public function testGroupErrorsByKey()
	{
		$data = [
			'key1' => 20,
			'key2' => []
		];

		try {

			Validate::lazy($data)
				->that('key1')->array()
				->that('key2')->string()
				->execute();
		}
		catch(InvalidLazyValidationException $error) {
			$errors = $error->getErrorsGroupedByKey();
			$this->assertArrayHasKey('key1', $errors);
			$this->assertArrayHasKey('key2', $errors);

			$this->assertSame(1, count($errors['key1']));
			$this->assertSame(1, count($errors['key2']));
		}
	}

	public function testUnknownType()
	{
		$this->expectException(\InvalidArgumentException::CLASS);
		$this->expectExceptionMessage('Unknown type');
		Validation::thisIsUnknown();
	}

	public function testInvalidOr()
	{
		$this->expectException(InvalidLazyValidationException::CLASS);
		$this->expectExceptionMessage('The following');

		Validate::lazy([
			'key1' => []
		])
		->that('key1')->string()->or()->integer()
		->execute();
	}

	public function testValidOr()
	{
		$data =
		Validate::lazy([
			'key1' => 20
		])
		->that('key1')->string()->or()->array()->or()->integer()->min(1)->max(30)
		->execute();

		$this->assertSame(20, $data['key1']);
	}

	public function testValidOptional()
	{
		$data = Validate::lazy([])
		->that('key1')->optional()->string()
		->that('key2')->optional(true)->boolean()
		->execute();

		$this->assertFalse(isset($data['key1']));
		$this->assertTrue($data['key2']);
	}

	public function testInvalidOptional()
	{
		$this->expectException(InvalidLazyValidationException::CLASS);
		$this->expectExceptionMessage('The following');

		Validate::lazy([
			'key' => null
		])
		->that('key')->optional(10)->string()
		->execute();
	}

	public function testValidAll()
	{
		['key' => $result] = Validate::lazy([
			'key' => [10, 20, 40]
		])
		->that('key')->all()->integer()
		->execute();

		$this->assertSame([10, 20, 40], $result);
	}

	public function testInvalidAll()
	{
		$this->expectException(InvalidLazyValidationException::CLASS);
		$this->expectExceptionMessage('The following 2');

		Validate::lazy([
			'array' => ['str', 20, 'ing']
		])
		->that('array')->all()->integer()
		->execute();
	}

	public function testErrorOnRequiredProperty()
	{
		$this->expectException(InvalidLazyValidationException::CLASS);
		$this->expectExceptionMessage('The following 1');

		Validate::lazy([])
			->that('missing')->string()
			->execute();
	}

	public function testDirectReturnIfValueMatchesDefault()
	{
		['def' => $value] = Validate::lazy(['def' => null])
			->that('def')->optional(null)->string()
			->execute();

		$this->assertSame(null, $value);
	}

	public function testValidKeyPatternLazyValidation()
	{
		$data = [
			'foo' => [
				['foo' => 1, 'fom' => 2],
				['foo' => 2, 'fom' => 3] 
			],
			'bar' => [
				['bar' => false, 'baz' => false], 
				['bar' => true, 'baz' => true]
			]
		];

		$result = Validate::lazy($data)
			->that('foo.*.foo')->integer()
			->that('foo.*.fom')->integer()
			->that('bar.*.bar')->boolean()
			->that('bar.*.baz')->boolean()
			->execute();

		$this->assertSame($data, $result);
	}

	public function testInvalidKeyPatternLazyValidation()
	{
		$this->expectException(InvalidLazyValidationException::CLASS);
		$this->expectExceptionMessage('The following 4');

		$data = [
			'foo' => [
				['foo' => 100],
				['foo' => 200]
			],
			'bar' => [
				['bar' => false], 
				['bar' => true]
			]
		];

		$result = Validate::lazy($data)
			->that('foo.*.foo')->boolean()
			->that('bar.*.bar')->integer()
			->execute();
	}

	public function testInvalidKeyPatternOnNoneArrayValue()
	{
		$this->expectException(InvalidLazyValidationException::CLASS);
		$this->expectExceptionMessage('The following 1');

		$result = Validate::lazy(['foo' => 10])
			->that('foo.bar')->integer()
			->execute();
	}

	public function testMissingKeyPattern()
	{
		$this->expectException(InvalidLazyValidationException::CLASS);
		$this->expectExceptionMessage('The following 2');

		$data = [
			'foo' => [
				['key' => 10],
				['key' => 10]
			]
		];

		$result = Validate::lazy($data)
			->that('foo.*.foo')->optional()->boolean()
			->that('foo.*.key')->integer()
			->that('foo.*.bar')->string()
			->execute();
	}
}

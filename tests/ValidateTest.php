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
			'unset' => null,
			'enum'  => 'ja'
		];

		$transformed = Validate::lazy($data)
			->that('date')->string()->dateTime('Y.m.d')
			->that('unset')->may()->integer()->between(10, 20)
			->that('enum')->enum(['ja' => true, 'nein' => false])
			->execute();


		$this->assertInstanceOf(\DateTime::CLASS, $transformed['date']);
		$this->assertNull($transformed['unset']);
		$this->assertTrue($transformed['enum']);
	}


	public function testInvalidLazyValidation()
	{
		$this->expectException(InvalidLazyValidationException::CLASS);
		$this->expectExceptionMessage('The following');

		$data = [
			'date'  => '2018.02.08',
			'unset' => null,
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
			'unset' => null,
			'enum'  => 'ja',
			'any'   => true,
			'none'  => ''
		];

		$transformed = Validate::lazy($data)
			->that('date')->string()->dateTime('Y.m.d')
			->that('unset')->may()->integer()->between(10, 20)
			->that('enum')->enum(['ja' => true, 'nein' => false])
			->that('any')
			->execute();


		$this->assertInstanceOf(\DateTime::CLASS, $transformed['date']);
		$this->assertNull($transformed['unset']);
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
				->that('key1')->all()->array()->string()
				->that('key2')->all()->string()->integer()
				->execute();
		}
		catch(InvalidLazyValidationException $error) {
			$errors = $error->getErrorsGroupedByKey();
			$this->assertArrayHasKey('key1', $errors);
			$this->assertArrayHasKey('key2', $errors);

			$this->assertSame(2, count($errors['key1']));
			$this->assertSame(2, count($errors['key2']));
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
		->that('key1')->string()->or()->array()->or()->all()->integer()->min(1)->max(30)
		->execute();

		$this->assertSame(20, $data['key1']);
	}
}

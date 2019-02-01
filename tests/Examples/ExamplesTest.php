<?php


use Everest\Validation\InvalidValidationException;
use Everest\Validation\Validate;
use Everest\Validation\Validation;

class ExampleTest extends \PHPUnit\Framework\TestCase {
	public function testExampleOne()
	{
		$this->expectException(InvalidValidationException::CLASS);

		$int = Validation::integer('10'); // $int -> 10
		$this->assertSame($int, 10);

		$noint = Validation::integer('foo'); // Will throw \Everest\Validation\InvalidValidationException
	}

	public function testExampleTwo()
	{
		$data = [
			'foo' => '10',
			'bar' => 'foo'
		];

		$data = Validate::lazy($data)
			->that('foo')->integer()->between(0, 20)
			->that('bar')->enum(['bar', 'foo'])->upperCase()
			->execute();

		// $data -> ['foo' => 10, 'bar' => 'FOO']
		$this->assertSame($data, ['foo' => 10, 'bar' => 'FOO']);
	}

	public function testExampleThree()
	{
		$data = [
			'foo' => [
				['bar' => 1, 'name' => 'Some name'],
				['bar' => 2, 'name' => 'Some other name']
			]
		];

		$data = Validate::lazy($data)
			->that('foo.*.bar')->integer()->between(0, 20)
			->that('foo.*.name')->string()->upperCase()
			->execute();

		// $data -> [
		//   'foo' => [
		//     ['bar' => 1, 'name' => 'SOME NAME'],
		//     ['bar' => 2, 'name' => 'SOME OTHER NAME']
		//   ]
		// ]

		$this->assertSame($data, [
			'foo' => [
				['bar' => 1, 'name' => 'SOME NAME'],
				['bar' => 2, 'name' => 'SOME OTHER NAME']
			]
		]);
	}
}
<?php

use Everest\Validation\Validation;
use Everest\Validation\Types\Type;

/**
 * @author  Philipp Steingrebe <philipp@steingrebe.de>
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'TypeTest.php';

class ValidationTest extends \PHPUnit\Framework\TestCase {

	public function testTransfromOnly()
	{
		$value = Validation::TransformInteger('foo') ?:
						 Validation::TransformInteger([]) ?:
						 Validation::TransformInteger('45');
		$this->assertSame(45, $value);
	}

	public function testAddingTypeClass()
	{
		Validation::addType('test', TypeTest::CLASS);
		$this->assertSame(Validation::test(12), 12);
	}

	public function testOverloadingTypeClass()
	{
		Validation::addType('array', TypeTest::CLASS, true);
		$this->assertSame(Validation::array(12), 12);
	}

	public function testInvalidOverloadingTypeClass()
	{
		$this->expectException(RuntimeException::CLASS);
		$this->expectExceptionMessage('Trying to overload type-class without setting overload flag.');

		Validation::addType('array', TypeTest::CLASS);
	}

	public function testAddingTypeInstance()
	{
		Validation::addType('test2', new TypeTest(2), true);
		$this->assertSame(Validation::test2(12), 24);
	}

	public function testOverloadingTypeInstance()
	{
		Validation::addType('test2', new TypeTest(3), true);
		$this->assertSame(Validation::test2(12), 36);
	}

	public function testInvalidOverloadingTypeInstance()
	{
		$this->expectException(RuntimeException::CLASS);
		$this->expectExceptionMessage('Trying to overload type-instance without setting overload flag.');

		Validation::addType('test2', new TypeTest(3));
	}
}

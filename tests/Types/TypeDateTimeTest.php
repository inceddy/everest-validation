<?php
use Everest\Validation\Types\TypeDateTime;
use Everest\Validation\InvalidValidationException;

class TypeDateTimeTest extends \PHPUnit\Framework\TestCase {

	public function testValidInput()
	{
		$value = (new TypeDateTime)('2018.02.08', 'Y.m.d');
		$this->assertInstanceOf(\DateTime::CLASS, $value);
		$this->assertSame('2018.02.08', $value->format('Y.m.d'));
	}

	public function testInvalidInput()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypeDateTime)('2018.02.08', 'd.m.Y');
	}

	public function testInvalidInputWithCustomMessage()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$this->expectExceptionMessage('Not a german date');
		$value = (new TypeDateTime)('2018.02.08', 'd.m.Y', 'Not a german date');
	}
}

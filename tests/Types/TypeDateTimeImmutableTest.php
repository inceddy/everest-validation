<?php
use Everest\Validation\Types\TypeDateTimeImmutable;
use Everest\Validation\InvalidValidationException;

class TypeDateTimeImmutableTest extends \PHPUnit\Framework\TestCase {

	public function testValidInput()
	{
		$value = (new TypeDateTimeImmutable)('2018.02.08', 'Y.m.d');
		$this->assertInstanceOf(\DateTimeImmutable::CLASS, $value);
		$this->assertSame('2018.02.08', $value->format('Y.m.d'));
	}

	public function testMutableConversion()
	{
		$value = (new TypeDateTimeImmutable)(new \DateTime('now'));
		$this->assertInstanceOf(\DateTimeImmutable::class, $value);
	}

	public function testInvalidInput()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$value = (new TypeDateTimeImmutable)('2018.02.08', 'd.m.Y');
	}

	public function testInvalidInputWithCustomMessage()
	{
		$this->expectException(InvalidValidationException::CLASS);
		$this->expectExceptionMessage('Not a german date');
		$value = (new TypeDateTimeImmutable)('2018.02.08', 'd.m.Y', 'Not a german date');
	}
}

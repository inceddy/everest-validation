<?php
use Everest\Validation\Type;
use Everest\Validation\Types\TypeDateTime;

class TypeDateTimeTest extends \PHPUnit_Framework_TestCase {

	public function testConstructionFromBaseType()
	{
		$this->assertInstanceOf(TypeDateTime::CLASS, Type::DateTime());
	}

	public function testInitialState()
	{
		$type = new TypeDateTime;
		$this->assertEquals('date_time', $type->getName());
	}

	public function testExecution()
	{
		$type = Type::DateTime('Y-m-d');

		// Success
		$this->assertTrue(($result = $type->execute('name', '1990-10-21'))->isValid());
		$this->assertEmpty($result->getErrorMessage());
		$this->assertInstanceOf(\DateTimeInterface::CLASS, $result->getTransformed());
		$this->assertFalse(($result = $type->execute('name', new stdClass))->isValid());
		$this->assertNotEmpty($result->getErrorMessage());
	}

	public function testInvalidConstruction()
	{
		$this->expectException(\Exception::CLASS);
		new TypeDateTime('Y-m-d', \StdClass::CLASS);
	}
}

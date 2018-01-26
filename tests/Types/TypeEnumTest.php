<?php
use Everest\Validation\Type;
use Everest\Validation\Types\TypeEnum;

class TypeEnumTest extends \PHPUnit\Framework\TestCase {

	public function testConstructionFromBaseType()
	{
		$this->assertInstanceOf(TypeEnum::CLASS, Type::Enum(['A']));
	}

	public function testInitialState()
	{
		$type = new TypeEnum(['A']);
		$this->assertEquals('enum', $type->getName());
	}

	public function testExecution()
	{
		$type = Type::Enum(['A' => false, 'B' => 10]);

		// Success
		$this->assertTrue(($result = $type->execute('name', 'B'))->isValid());
		$this->assertEmpty($result->getErrorDescription());
		$this->assertEquals(10, $result->getTransformed());
		$this->assertFalse(($result = $type->execute('name', 'C'))->isValid());
		$this->assertNotEmpty($result->getErrorDescription());
	}

	public function testInvalidConstruction()
	{
		$this->expectException(Exception::CLASS);
		new TypeEnum([]);
	}
}

<?php

use Everest\Validation\Type;
use Everest\Validation\Types\TypeClosure;


class TypeClosureTest extends \PHPUnit_Framework_TestCase {

  public function __construct(... $arguments) {
  	parent::__construct(... $arguments);
    $this->λ  = function(){
    };
  }

	public function testConstructionFromBaseType()
	{
		$this->assertInstanceOf(TypeClosure::CLASS, Type::Closure($this->λ, $this->λ));
	}

	public function testInitialState()
	{
		$type = new TypeClosure($this->λ, $this->λ, null, 'some_type');
		$this->assertEquals('some_type', $type->getName());
	}

	public function testExecution()
	{
		$type = new TypeClosure(function(int $value) {
			return $value > 50;
		}, $this->λ);

		// Success
		$result = $type->execute('name', 100);
		$this->assertTrue($result->isValid());
		$this->assertEmpty($result->getErrorMessage());
		// Failure
		$result = $type->execute('name', 0);
		$this->assertFalse($result->isValid());
		$this->assertNotEmpty($result->getErrorMessage());
	}

	public function testTransform()
	{
		// Trim is default option
		$type = new TypeClosure(function($value){
			return true;
		}, function(int $value) {
			return $value + 50;
		});
		$result = $type->execute('name', 50);
		$this->assertEquals(100, $result->getTransformed());
	}
}

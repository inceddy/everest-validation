<?php

use Everest\Validation\Type;
use Everest\Validation\Types\{
	TypeString,
	TypeInt
};
use Everest\Validation\Expressions\{
	ExpressionAnd,
	ExpressionOr,
	ExpressionRequired,
	ExpressionOptional
};


class ExpressionsTest extends \PHPUnit_Framework_TestCase {

	public function testConstructionFromBaseType()
	{
		$this->assertInstanceOf(ExpressionOr::CLASS,       Type::Or(Type::Int(), Type::String()));
		$this->assertInstanceOf(ExpressionAnd::CLASS,      Type::And(Type::Int(), Type::Int(30)));
	}

	public function testOptional()
	{
		$type = new ExpressionOptional(new TypeInt);

		$this->assertTrue($type->execute('name', null)->isValid());
		$this->assertTrue($type->execute('name', '')->isValid());
		$this->assertTrue($type->execute('name', [])->isValid());

		$type = new ExpressionOptional(new TypeInt, ExpressionOptional::FILTER | ExpressionOptional::TRIM);
		$this->assertTrue($type->execute('name', '   ')->isValid());
		$this->assertTrue($type->execute('name', [null, ''])->isValid());


		$this->assertTrue($type->execute('name', 19)->isValid());
	}

	public function testRequired()
	{
		$type = new ExpressionRequired(new TypeInt);

		$this->assertFalse($type->execute('name', null)->isValid());
		$this->assertFalse($type->execute('name', '')->isValid());
		$this->assertFalse($type->execute('name', [])->isValid());

		$type = new ExpressionRequired(new TypeInt, ExpressionRequired::FILTER | ExpressionRequired::TRIM);
		$this->assertFalse($type->execute('name', '   ')->isValid());
		$this->assertFalse($type->execute('name', [null, ''])->isValid());


		$this->assertTrue($type->execute('name', 19)->isValid());
	}

	public function testAnd()
	{
		$type = new ExpressionAnd(Type::Int(), Type::IntBetween(30));
		// Success
		$this->assertTrue($type->execute('name', 40)->isValid());
		// Failure
		$this->assertFalse($type->execute('name', 10)->isValid());
	}

	public function testOr()
	{
		$type = new ExpressionOr(Type::Int(), Type::String());
		// Success
		$this->assertTrue($type->execute('name', 40)->isValid());
		$this->assertTrue($type->execute('name', 'Foo')->isValid());
		// Failure
		$this->assertFalse($type->execute('name', [])->isValid());
	}
}

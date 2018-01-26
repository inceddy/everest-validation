<?php

use Everest\Validation\Validator;
use Everest\Validation\Type;
use Everest\Validation\Types\{
	TypeArray,
	TypeInt
};
use Everest\Validation\Expressions\{
	ExpressionRequired,
	ExpressionOptional
};


/**
 * @author  Philipp Steingrebe <philipp@steingrebe.de>
 */

class TypeTest extends \PHPUnit\Framework\TestCase {

	public function testAutoRequired()
	{
		$this->assertInstanceOf(ExpressionRequired::CLASS, Type::RequiredString());
	}

	public function testAutoOptional()
	{
		$this->assertInstanceOf(ExpressionOptional::CLASS, Type::OptionalString());
	}
}

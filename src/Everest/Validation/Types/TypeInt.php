<?php

/*
 * This file is part of Everest.
 *
 * (c) 2017 Philipp Steingrebe <development@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Everest\Validation\Types;
use Everest\Validation\TypeInterface;
use Everest\Validation\TypeResult;

class TypeInt implements TypeInterface {

	/**
	 * {@inheritDoc}
	 */

	public function execute(string $name, $value) : TypeResult
	{
		return is_numeric($value) && floatval($value) == intval($value) ?
			TypeResult::success($name, (int) $value) :
			TypeResult::failure($name, '\'%s\' is not a valid integer.');
	}


	/**
	 * {@inheritDoc}
	 */

	public function getName() : string 
	{
		return 'int';
	}
}

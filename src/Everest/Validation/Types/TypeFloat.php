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

class TypeFloat implements TypeInterface {

	/**
	 * {@inheritDoc}
	 */

	public function execute(string $name, $value) : TypeResult
	{
		return is_numeric($value) ?
			TypeResult::success($name, (float)$value) : 
			TypeResult::failure($name, $this->getName(), '%s is not a valid float.');
	}


	/**
	 * {@inheritDoc}
	 */

	public function getName() : string 
	{
		return 'float';
	}
}

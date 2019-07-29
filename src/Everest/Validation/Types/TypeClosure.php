<?php

/*
 * This file is part of Everest.
 *
 * (c) 2019 Philipp Steingrebe <development@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Everest\Validation\Types;
use Everest\Validation\InvalidValidationException;

class TypeClosure extends Type {


	public function __invoke($value, \Closure $handler, $message = null, string $key = null)
	{
		return $handler($value, $message, $key);
	}
}

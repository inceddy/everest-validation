<?php

/*
 * This file is part of Everest.
 *
 * (c) 2017 Philipp Steingrebe <development@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Everest\Validation;

interface TypeInterface {
	/**
	 * Execute the type on the given value.
	 *
	 * @param string $value
	 *   The name of the value
	 * @param mixed $value
	 *   The value to test
	 *
	 * @return Result
	 *    The execution result set
	 */
	
	public function execute(string $name, $value) : TypeResult;

	public function getName() : string;
}

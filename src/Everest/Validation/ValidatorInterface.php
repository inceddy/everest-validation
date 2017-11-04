<?php

/*
 * This file is part of Everest.
 *
 * (c) 2017 Philipp Steingrebe <development@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Everest\Validator;

use InvalidArgumentException;
use LogicException;

/**
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 * @package Everest\Http
 */

interface ValidatorInterface {


	public function initials(array $initials);

	/**
	 * Set initial value for property to test
	 *
	 * @param string $name
	 *   The key of the value
	 * @param mixed $value
	 *   The initial value to this key
	 *
	 * @return self
	 */
	
	public function initial(string $name, $value = null);


	/**
	 * Define a validation type for a property
	 * and set an optional default value.
	 *
	 * @param string $name
	 *   The name of the property to validate
	 * @param mixed $initial
	 *   The initial value (optional)
	 * @param Everest\Validator\TypeInterface $type
	 *   The to validate the value with
	 *
	 * @return self
	 */
	
	public function validate(string $name, $initial = null, $type);


	/**
	 * Runs the validation process
	 * 
	 * @param mixed $stroe
	 *   The data store to validate
	 *
	 * @return self
	 */
	
	public function execute($store);
}

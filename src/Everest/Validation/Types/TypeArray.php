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
use Everest\Validation\InvalidValidationException;

class TypeArray extends Type {

	public static $errorName = 'invalid_array';
	public static $errorMessage = '%s is not a valid array';

	public function __invoke($value, $message = null, string $key = null)
	{
		if (!is_array($value)) {

			$message = sprintf(
				static::generateErrorMessage($message ?: '%s is not a valid array'),
				static::stringify($value)
			);

			throw new InvalidValidationException(self::$errorName, $message, $key, $value);
		}

		return $value;
	}
}

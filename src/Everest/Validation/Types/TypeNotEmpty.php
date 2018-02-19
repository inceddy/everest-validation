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

class TypeNotEmpty extends Type {

	public static $errorName = 'value_missing';
	public static $errorMessage = '%s is not a valid array';

	public function __invoke($value, $message = null, string $key = null)
	{
		if (empty($value)) {

			$message = sprintf(
				static::generateErrorMessage($message ?: self::$errorMessage),
				static::stringify($value)
			);

			throw new InvalidValidationException(self::$errorName, $message, $key, $value);
		}

		return $value;
	}
}

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

class TypeLengthBetween extends Type {

	public static $errorName = 'invalid_length_between';
	public static $errorMessage = 'The length of %s is not between %s and %s.';

	public function __invoke($value, $min, $max, $message = null, string $key = null)
	{
		$length = strlen($value);

		if ($length < $min || $length > $max) {
			$message = sprintf(
				self::generateErrorMessage($message ?: self::$errorMessage),
				self::stringify($value),
				self::stringify($min),
				self::stringify($max)
			);

			throw new InvalidValidationException(self::$errorName, $message, $key, $value);
		}

		return $value;
	}
}
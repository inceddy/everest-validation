<?php

/*
 * This file is part of Everest.
 *
 * (c) 2018 Philipp Steingrebe <development@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Everest\Validation\Types;
use Everest\Validation\InvalidValidationException;

class TypeMin extends Type {

	public static $errorName = 'invalid_min';
	public static $errorMessage = '%s is lower than %s.';

	public function __invoke($value, $min, $message = null, string $key = null)
	{
		if ($value < $min) {
			$message = sprintf(
				self::generateErrorMessage($message ?: self::$errorMessage),
				self::stringify($value),
				self::stringify($min)
			);

			throw new InvalidValidationException(self::$errorName, $message, $key, $value);
		}

		return $value;
	}
}

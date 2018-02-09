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

class TypeBoolean extends Type {

	public static $errorName = 'invalid_boolean';
	public static $errorMessage = '%s is not a valid boolean or like a boolean.';

	public function __invoke($value, $message = null, string $key = null)
	{
		if (!in_array($value, [true, false, 'true', 'false', 1, 0, '1', '0'], true)) {
			$message = sprintf(
				self::generateErrorMessage($message ?: self::$errorMessage),
				self::stringify($value)
			);

			throw new InvalidValidationException(self::$errorName, $message, $key, $value);
		}

		return in_array($value, [true, 'true', 1, '1'], true);
	}
}

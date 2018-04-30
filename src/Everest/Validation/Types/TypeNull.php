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

class TypeNull extends Type {

	public static $errorName = 'not_null';
	public static $errorMessage = '%s is not <NULL>.';

	public function __invoke($value, $message = null, string $key = null)
	{
		if (null !== $value) {
			$message = sprintf(
				self::generateErrorMessage($message ?: self::$errorMessage),
				self::stringify($value)
			);

			throw new InvalidValidationException(self::$errorName, $message, $key, $value);
		}

		return $value;
	}
}

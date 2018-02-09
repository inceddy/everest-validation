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

class TypeEnum extends Type {

	public static $errorName = 'invalid_enum';
	public static $errorMessage = '%s is an invalid choice. Expected one of [%s].';

	public function __invoke($value, array $choices, $message = null, string $key = null)
	{
		// Transform sequenz to assoc
		if (empty(array_filter(array_keys($choices), 'is_string'))) {
			$choices = array_combine($choices, $choices);
		}

		if (!array_key_exists((new TypeString)($value), $choices)) {

			$message = sprintf(
				self::generateErrorMessage($message ?: self::$errorMessage),
				self::stringify($value),
				implode(', ', array_keys($choices))
			);

			throw new InvalidValidationException (
				self::$errorName,
				$message,
				$key,
				$value
			);
		}

		return $choices[$value];
	}
}

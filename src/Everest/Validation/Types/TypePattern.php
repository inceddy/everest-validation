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

class TypePattern extends Type {

	public static $errorName = 'pattern_mismatch';
	public static $errorMessage = '%s does not match the required pattern (%s)';

	public function __invoke($value, string $pattern, $message = null, string $key = null)
	{
		if (!preg_match($pattern, $value)) {
			$message = sprintf(
				self::generateErrorMessage($message ?: self::$errorMessage),
				self::stringify($value),
				self::stringify($pattern)
			);

			throw new InvalidValidationException(self::$errorName, $message, $key, $value);
		}

		return $value;
	}
}
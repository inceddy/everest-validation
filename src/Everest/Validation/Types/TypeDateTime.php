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
use Everest\Validation\Validation;

class TypeDateTime extends Type {

	public static $errorName = 'invalid_datetime';
	public static $errorMessage = '%s is not a valid date of format %s.';

	public function __invoke($value, $format = \DateTime::ATOM, $message = null, string $key = null)
	{
		if ($value instanceof \DateTime) {
			return $value;
		}

		$dateTime = \DateTime::createFromFormat(
			Validation::String($format),
			Validation::String($value)
		);

		if (!$dateTime || $value !== $dateTime->format($format)) {
			$message = sprintf(
				self::generateErrorMessage($message ?: self::$errorMessage),
				self::stringify($value),
				self::stringify($format)
			);

			throw new InvalidValidationException(self::$errorName, $message, $key, $value);
		}

		return $dateTime;
	}
}

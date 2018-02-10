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
use Everest\Validation\Validation;
use Everest\Validation\InvalidValidationException;

class TypeKeyExists extends Type {

	public static $errorName = 'missing_key';
	public static $errorMessage = 'Key %s does not exist.';

	public function __invoke($value, string $akey, $message = null, string $key = null)
	{
		Validation::Array($value);

		if (!array_key_exists($akey, $value)) {

			$message = sprintf(
				static::generateErrorMessage($message ?: self::$errorMessage),
				static::stringify($akey)
			);

			throw new InvalidValidationException(self::$errorName, $message, $key, $value);
		}

		return $value;
	}
}

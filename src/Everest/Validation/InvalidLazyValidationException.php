<?php

/*
 * This file is part of Everest.
 *
 * (c) 2018 Philipp Steingrebe <development@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Everest\Validation;

class InvalidLazyValidationException extends InvalidValidationException
{
	public static function fromErrors(array $errors, $message = null)
	{
		$message = sprintf('The following %d validations failed:', count($errors)) . "\n";

		$i = 1;
		foreach ($errors as $error) {
				$message .= sprintf("%d) %s: %s\n", $i++, $error->getKey(), $error->getMessage());
		}
		return new static($message, $errors);
	}

	public function __construct(string $message = null, array $errors)
	{
		parent::__construct('', $message, null, null);

		$this->errors = $errors;
	}

	public function getErrors() : array
	{
		return $this->errors;
	}

	public function getErrorsGroupedByKey() : array
	{
		$errors = [];

		foreach ($this->errors as $error) {
			$key = $error->getKey();
			if (!isset($errors[$key])) {
				$errors[$key] = [];
			}

			$errors[$key][] = $error;
		}

		return $errors;
	}

	public function merge(InvalidValidationException $error) : InvalidLazyValidationException
	{
		if ($error instanceof InvalidLazyValidationException) {
			return self::fromErrors(array_merge($this->errors, $error->getErrors()));
		}

		return self::fromErrors(array_merge($this->errors, [$error]));
	}
}
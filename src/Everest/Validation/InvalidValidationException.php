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

class InvalidValidationException extends \InvalidArgumentException
{
	public function __construct(string $name, string $message = null, string $key = null, $value)
	{
		parent::__construct($message);

		$this->name  = $name;
		$this->key   = $key;
		$this->value = $value;
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getKey()
	{
		return $this->key;
	}

	public function merge(InvalidValidationException $error) : InvalidLazyValidationException
	{
		if ($error instanceof InvalidLazyValidationException) {
			return $error->merge($this);
		}

		return InvalidLazyValidationException::fromErrors([
			$this,
			$error
		]);
	}
}
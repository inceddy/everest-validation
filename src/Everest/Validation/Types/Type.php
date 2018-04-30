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

use Everest\Validation\Undefined;

abstract class Type {

	public static function stringify($value) : string
	{
		switch (true) {
			case is_scalar($value):
				$value = (string) $value;
				return mb_strlen($value) < 100 ?
					$value :
					substr($value, 0, 97) . '...';
			case is_array($value):
				return '<ARRAY>';
			case is_bool($value):
				return $value ? '<TRUE>' : '<FALSE>';
			case is_object($value) && $value instanceof Undefined:
				return '<UNDEFINED>';
			case is_object($value):
				return get_class($value);
			case is_resource($value):
				return get_resource_type($value);
			case is_null($value):
				return '<NULL>';
			default:
				return gettype($value);
		}
	}


	public static function generateErrorMessage($message = null) :? string
	{
		if (is_callable($message)) {
			$traces = debug_backtrace(0);
			$args = [];

			$reflection = new \ReflectionClass($traces[1]['class']);
			$method = $reflection->getMethod($traces[1]['function']);

			foreach ($method->getParameters() as $index => $parameter) {
				$name = $parameter->getName();

				if ('message' === $name) {
					continue;
				}
				
				$args[$name] = array_key_exists($index, $traces[1]['args']) ? 
					$traces[1]['args'][$index] :
					$parameter->getDefaultValue();
			}

			$args['type'] = $traces[1]['class'];
			$message = call_user_func($message, $args);
		}

		return null === $message ? null : (string) $message;
	}
}
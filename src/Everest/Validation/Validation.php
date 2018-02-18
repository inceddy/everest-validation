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
use Everest\Validation\Types\Type;

final class Validation {

	private static $typeMap = [
		// String related
		'string'            => Types\TypeString::CLASS,
		'length'            => Types\TypeLength::CLASS,
		'lengthbetween'     => Types\TypeLengthBetween::CLASS,
		'lengthmax'         => Types\TypeLengthMax::CLASS,
		'lengthmin'         => Types\TypeLengthMin::CLASS,

		// Date
		'datetime'          => Types\TypeDateTime::CLASS,
		'datetimeimmutable' => Types\TypeDateTimeImmutable::CLASS,

		// Numerical
		'integer'           => Types\TypeInteger::CLASS,
		'float'             => Types\TypeFloat::CLASS,
		'between'           => Types\TypeBetween::CLASS,
		'max'               => Types\TypeMax::CLASS,
		'min'               => Types\TypeMin::CLASS,

		// Array related
		'array'             => Types\TypeArray::CLASS,
		'enum'              => Types\TypeEnum::CLASS,
		'keyexists'         => Types\TypeKeyExists::CLASS,

		// Boolean
		'boolean'           => Types\TypeBoolean::CLASS,

		// Filter
		'trim'              => Filter\FilterTrim::CLASS,
		'lowercase'         => Filter\FilterLowerCase::CLASS,
		'uppercase'         => Filter\FilterUpperCase::CLASS,
	];


	/**
	 * Type instances
	 * @var array
	 */
	
	private static $instances = [];


	/**
	 * Defines a new type
	 *
	 * @throws InvalidArgumentException
	 *    If the given type class does not exist
	 * @throws RuntimeException
	 *    If the method call would overload an existing type
	 *    without setting the overload flag
	 *
	 * @param string $name
	 *    The type name
	 * @param string $typeClassName
	 *    The type class
	 * @param bool $overload
	 *    Whether or not to overload existing types
	 */
	
	public static function addType(string $name, string $typeClassName, bool $overload = false)
	{
		$name = strtolower($name);
		
		if (!$overload && isset(self::$typeMap[$name])) {
			throw new \RuntimeException('Trying to overload type without setting overload flag.');
		}

		if (!class_exists($typeClassName)) {
			throw new \InvalidArgumentException(sprintf('Unkown class %s.', $typeClassName));
		}

		if (!is_subclass_of($typeClassName, Type::CLASS)) {
			throw new \InvalidArgumentException(sprintf(
				'%s does extend from %s.', 
				$typeClassName,
				Type::CLASS
			));
		}

		self::$typeMap[$name] = $typeClassName;
	}


	/**
	 * Returns a type instance.
	 *
	 * The type name might be prefix with 'optional' to
	 * automaticly wrap the type in an ExpressionOptional-type.
	 *
	 * @throws InvalidArgumentException
	 *    If the given type is unknown
	 *
	 * @param string $name
	 *   The type name
	 * @param array $arguments
	 *   The arguments to construct the type instance with
	 *
	 * @return Everest\Validation\Types\TypeInterface
	 */
	
	public static function __callStatic($name, $args) 
	{
		$name = strtolower($name);
		
		if ($trans = strpos($name, 'transform') === 0) {
			$name = substr($name, 9);
		}


		if (!isset(self::$typeMap[$name])) {
			throw new \InvalidArgumentException(
				sprintf('Unknown type \'%s\' please use one of [%s].', 
					$name, 
					implode(', ', array_keys(self::$typeMap))
				)
			);
		}


		$className = self::$typeMap[$name];

		$type = self::$instances[$className] ?? 
						self::$instances[$className] = new $className();

		if (!$trans) {
			return $type(... $args);
		}

		try {
			return $type(... $args);
		} 
		catch (InvalidValidationException $e) {
			return null;
		}
	}
}

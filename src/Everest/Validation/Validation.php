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

final class Validation implements \ArrayAccess {

	private static $typeMap = [
		// String related
		'string'            => Types\TypeString::class,
		'length'            => Types\TypeLength::class,
		'lengthbetween'     => Types\TypeLengthBetween::class,
		'lengthmax'         => Types\TypeLengthMax::class,
		'lengthmin'         => Types\TypeLengthMin::class,
		'pattern'           => Types\TypePattern::class,

		// Logic
		'notempty'          => Types\TypeNotEmpty::class,

		// Date
		'datetime'          => Types\TypeDateTime::class,
		'datetimeimmutable' => Types\TypeDateTimeImmutable::class,

		// Numerical
		'integer'           => Types\TypeInteger::class,
		'float'             => Types\TypeFloat::class,
		'between'           => Types\TypeBetween::class,
		'max'               => Types\TypeMax::class,
		'min'               => Types\TypeMin::class,

		// Array related
		'array'             => Types\TypeArray::class,
		'enum'              => Types\TypeEnum::class,
		'keyexists'         => Types\TypeKeyExists::class,

		// Boolean
		'boolean'           => Types\TypeBoolean::class,

		// Other
		'null'              => Types\TypeNull::class,
		'closure'           => Types\TypeClosure::class,

		// Filter
		'trim'              => Filter\FilterTrim::class,
		'lowercase'         => Filter\FilterLowerCase::class,
		'uppercase'         => Filter\FilterUpperCase::class,
		'striptags'         => Filter\FilterStripTags::class
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
	 * @param mixed $type
	 *    The type class
	 * @param bool $overload
	 *    Whether or not to overload existing types
	 */
	
	public static function addType(string $name, $type, bool $overload = false)
	{
		$name = strtolower($name);

		if (is_string($type)) {
			if (!$overload && isset(self::$typeMap[$name])) {
				throw new \RuntimeException(
					'Trying to overload type-class without setting overload flag.'
				);
			}

			if (!class_exists($type)) {
				throw new \InvalidArgumentException(sprintf(
					'Unkown type-class %s.', $type
				));
			}

			if (!is_subclass_of($type, Type::class)) {
				throw new \InvalidArgumentException(sprintf(
					'Type-class %s does extend from %s.', $type,	Type::class
				));
			}

			// Remove exisiting instance
			unset(self::$instances[$type]);

			self::$typeMap[$name] = $type;
			return true;
		}

		if (is_object($type)) {
			$className = get_class($type);

			if (!$overload && isset(self::$instances[$className])) {
				throw new \RuntimeException(
					'Trying to overload type-instance without setting overload flag.'
				);
			}

			if (!$type instanceof Type) {
				throw new \InvalidArgumentException(sprintf(
					'Type-instance %s does extend from %s.', get_class($type),	Type::class
				));
			}

			self::$typeMap[$name] = $className; 
			self::$instances[$className] = $type;
			return true;
		}

		throw new \InvalidArgumentException(
			'Supplied argument is not a valid type-class nor a valid type-instance.'
		);
	}

	public static function getTypeInstance(string $name)
	{
		$className = self::$typeMap[$name];
		return self::$instances[$className] ?? self::$instances[$className] = new $className();
	}

	public static function getTypeParameterCount(string $name)
	{
		static $counts = [];

		$name = strtolower($name);

		if (isset($counts[$name])) {
			return $counts[$name];
		}

		$type = self::getTypeInstance($name);

		return $counts[$name] = (new \ReflectionMethod($type, '__invoke'))->getNumberOfParameters();
	}


	/**
	 * Returns the result of the type execution.
	 *
	 * @throws InvalidArgumentException
	 *   If the supplied type-name is unknown
	 * @throws Everest\Validation\InvalidValidationException
	 *   If the supplied argument is invalid for the type
	 *   One can prefix type-name with `transform` to return
	 *   `null` in invalid cases instead of throwing the exception.
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


		$type = self::getTypeInstance($name);

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

	public function offsetExists(mixed $offset) : bool
	{
		return isset(self::$typeMap[$offset]);
	}

	public function offsetGet (mixed $offset) : mixed
	{
		return self::$typeMap[$offset];
	}

	public function offsetSet(mixed $offset, mixed $value) : void 
	{
		throw new \Exception('Not implemented');
	}

	public function offsetUnset(mixed $offset) : void
	{
		throw new \Exception('Not implemented');
	}
}

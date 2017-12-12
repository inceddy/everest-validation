<?php

/*
 * This file is part of Everest.
 *
 * (c) 2017 Philipp Steingrebe <development@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Everest\Validation;
use InvalidArgumentException;

final class Type {

  private static $typeMap = [
    // Expressions
    'and'               => Expressions\ExpressionAnd::CLASS,
    'or'                => Expressions\ExpressionOr::CLASS,
    'required'          => Expressions\ExpressionRequired::CLASS,

    // String related
    'string'            => Types\TypeString::CLASS,
    'length'            => Types\TypeStringLength::CLASS,
    'regex'             => Types\TypeStringRegEx::CLASS,

    // Date
    'datetime'          => Types\TypeDateTime::CLASS,

    // Numerical
    'int'               => Types\TypeInt::CLASS,
    'intbetween'        => Types\TypeIntBetween::CLASS,
    'float'             => Types\TypeFloat::CLASS,
    'floatbetween'      => Types\TypeFloatBetween::CLASS,

    // Array related
    'array'             => Types\TypeArray::CLASS,
    'enum'              => Types\TypeEnum::CLASS,
    'struct'            => Types\TypeStruct::CLASS,

    // Misc
    'closure'           => Types\TypeClosure::CLASS,
    'any'               => Types\TypeAny::CLASS
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
    if (!$overload && isset(self::$typeMap[$name])) {
      throw new RuntimeException('Trying to overload type without setting overload flag.');
    }

    if (!class_exists($typeClassName)) {
      throw new InvalidArgumentException(sprintf('Unkown class %s.', $typeClassName));
    }

    if (!is_subclass_of($typeClassName, TypeInterface::CLASS)) {
      throw new InvalidArgumentException(sprintf(
        '%s does not implement the %s interface.', 
        $typeClassName,
        TypeInterface::CLASS
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
  
  public static function __callStatic($name, $arguments) 
  {
    $optional = 
    $required = false;

    $name = strtolower($name);

    if (0 === strpos($name, 'required')) {
      $required = true;
      $name = substr($name, 8);
    }

    else if (0 === strpos($name, 'optional')) {
      $optional = true;
      $name = substr($name, 8);
    }

    if (!$className = self::$typeMap[$name] ?? null) {
      throw new InvalidArgumentException(
        sprintf('Unkown type \'%s\' please use one of [%s].', 
          $name, 
          implode(', ', array_keys(self::$typeMap))
        )
      );
    }

    if (empty($arguments)) {
      $type = self::$instances[$className] ?? 
              self::$instances[$className] = new $className();
    }
    else {
      $type = new $className(...$arguments);
    }

    if ($optional) {
      $type = new Expressions\ExpressionOptional($type);
    }
    elseif ($required) {
      $type = new Expressions\ExpressionRequired($type);
    }

    return $type;
  }
}

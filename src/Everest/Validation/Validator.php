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
use Everest\Validation\Types\TypeAny;
use InvalidArgumentException;
use LogicException;

/**
 * Validator for user input.
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 * @package Everest\Http
 */

final class Validator {

  /**
   * Initial values for parameters
   * @var array
   */
  
  private $initials = [];


  /**
   * The types to be executed on the request as
   * key => [AssertInterface, AssertInterface, ...]
   *
   * @var array
   */
  
  private $types = [];


  /**
   * Sets a default value for the given name
   *
   * @param string $name
   *   The name the default value targets
   * @param mixed $value
   *   The default value
   *
   * @return self
   */
  
  public function initial(string $name, $value = null)
  {
    $this->initials[$name] = $value;
    return $this;
  }


  /**
   * Defines a validation for a name with optional default value
   *
   * @param string $name
   *   The name to be validated
   * @param mixed $initial
   *   The default value or type if none default 
   *   value is provided
   * @param TypeInterface|null $type
   *   The type for validate
   *
   * @return self
   */
  
  public function validate(string $name, $initial, TypeInterface $type = null)
  {
    if ($initial instanceof TypeInterface) {
      $type = $initial;
    }
    else {
      $this->initial($name, $initial);
    }

    $this->types[$name] = $type ?: new TypeAny;
    return $this;
  }


  /**
   * Runs the validation process
   * @return Everest\Validation\ValidationResult
   */
  
  public function execute($store) : ValidationResult
  {
    if (is_object($store) && method_exists($store, 'toArray')) {
      $store = $store->toArray();
    }

    if (!is_array($store)) {
      throw new InvalidArgumentException(
        sprintf(
          'Store MUST be an array or provide a toArray-method but %s given.',
          is_object($store) ? get_class($store) : gettype($store)
        )
      );
    }

    $validationResult = new ValidationResult;

    foreach ($this->types as $name => $type) {
      $value = $store[$name] ?? $this->initials[$name] ?? null;
      $validationResult->addTypeResult(
        $type->execute($name, $value, $validationResult)
      );
    }

    return $validationResult;
  }
}

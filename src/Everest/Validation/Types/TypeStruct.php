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
use Everest\Validation\TypeInterface;
use Everest\Validation\TypeResult;
use InvalidArgumentException;
use LogicException;

class TypeStruct implements TypeInterface {

  private const FAIL_NOT_ARRAY  = 'not_array';
  private const FAIL_WRONG_TYPE = 'wrong_type';

  private $types;

  public function __construct(array $types) 
  {

    if (empty($types)) {
      throw new LogicException('There MUST be at least one type');
    }

    foreach ($types as $key => $type) {
      if (!is_string($key)) {
        throw new InvalidArgumentException(sprintf(
          'Keys MUST be strings but %s given', 
          gettype($key)
        ));
      }

      if (!$type instanceof TypeInterface) {
        throw new InvalidArgumentException(sprintf(
          'Values MUST implement TypeInterface but %s given', 
          is_object($type) ? get_class($type) : gettype($type)
        ));
      }
    }

    $this->types = $types;
  }

  private function fail(string $name, string $reason, ... $args) : TypeResult
  {
    return TypeResult::failure($name, $reason, function($name) use ($reason, $args){
      switch ($reason) {
        case self::FAIL_NOT_ARRAY:
          return sprintf('\'%s\' is not an array.', $name);

        case self::FAIL_WRONG_TYPE:
          [$key, $result] = $args;
          return sprintf(
            'Wrong type for \'%s[%s]\': %s.', 
            $name, $key, $result->getErrorDescription()
          );
      }
    });
  }


  /**
   * {@inheritDoc}
   */

  public function execute(string $name, $values) : TypeResult
  {
    if (!is_array($values)) {
      return $this->fail($name, self::FAIL_NOT_ARRAY);
    }

    $transformed = [];
    foreach ($this->types as $key => $type) {
      if (!($result = $type->execute($key, $values[$key] ?? null))->isValid()) {
        return $this->fail($name, self::FAIL_WRONG_TYPE, $key, $result);
      }

      $transformed[$key] = $result->getTransformed();
    }

    return TypeResult::success($name, $transformed);
  }


  /**
   * {@inheritDoc}
   */

  public function getName() : string
  {
    return 'struct';
  }
}

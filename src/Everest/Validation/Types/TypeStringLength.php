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
use Everest\Validation\TypeResult;

class TypeStringLength extends TypeString {

  private $min;

  private $max;

  public function __construct($min = 0, $max = INF, int $options = self::TRIM) 
  {
    parent::__construct($options);

    if ($min >= $max) {
      throw new \LogicException('Max length cant be lower than min length.');
    }

    $this->min  = $min;
    $this->max  = $max;
  }


  /**
   * {@inheritDoc}
   */
  
  public function execute(string $name, $value) : TypeResult
  {
    if (!($result = parent::execute($name, $value))->isValid()) {
      return $result;
    }

    $length = strlen($result->getTransformed());
    $min = $this->min <= $length;
    $max = $this->max >= $length;

    $valid = $min && $max;
    $reason = !$valid && !$min ? 'too_short' : 'too_long';

    return $valid ?
      $result : 
      TypeResult::failure($name, $reason, function($name) use ($min, $max) {
        switch (true) {
          case !$min:
            return sprintf('\'%s\' must be longer than %s.', $name, $this->min);
          case !$max:
            return sprintf('\'%s\' must be shorter than %s.', $name, $this->max);
        }
      });
  }
}

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

class TypeIntBetween extends TypeInt {

  /**
   * Minumum value (including)
   * @var int
   */
  
  private $min;

  /**
   * Maximum value (including)
   * @var int
   */
  
  private $max;

  public function __construct(int $min = null, int $max = null) 
  {

    $this->min = $min ?? -INF;
    $this->max = $max ?? +INF;
  }


  /**
   * {@inheritDoc}
   */

  public function execute(string $name, $value) : TypeResult
  {
    if (!($result = parent::execute($name, $value))->isValid()) {
      return $result;
    }

    return ($this->min <= $value && $value <= $this->max) ?
      $result :
      TypeResult::failure($name, 'int_between', function($name){
        return sprintf(
          'The value of \'%s\' is not between %s and %s.', 
          $name, $this->min, $this->max
        );
      });
  }
}

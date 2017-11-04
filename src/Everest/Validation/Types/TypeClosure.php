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

class TypeClosure implements TypeInterface {

  /**
   * Callback for validation
   * @var Closure
   */
  
  private $validator;


  /**
   * Callback for transform-method
   * @var Closure
   */
  
  private $transform;

  private $errorMessageResolver;

  private $name;

  public function __construct(
    \Closure $validator, 
    \Closure $transform = null, 
    \Closure $errorMessageResolver = null,
    string $name = 'custom'
  )
  {
    $this->validator = $validator;
    $this->transform = $transform;
    $errorMessageResolver = $errorMessageResolver;
    $this->name = $name;
  }


  /**
   * {@inheritDoc}
   */

  public function execute(string $name, $value) : TypeResult
  {
    return ($this->validator)($value) ?
      TypeResult::success($name, $this->transform ? ($this->transform)($value) : $value) : 
      TypeResult::failure($name, $this->errorMessageResolver ?: function($name){
        return 'An unspecified error occured validating ' . $name;
      });
  }

  public function getName() : string
  {
    return $this->name;
  }
}

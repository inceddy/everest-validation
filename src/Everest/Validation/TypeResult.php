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

final class TypeResult {

  /**
   * State of this result
   * @var bool
   */
  
  private $valid;

  /**
   * The name of the validated value
   * @var string
   */
  
  private $name;

  /**
   * The transformed value
   * @var mixed
   */
  
  private $transformed;

  /**
   * The error name or `null` if valid
   * @var string
   */
  
  private $errorName;

  /**
   * The error description or `null` if valid
   * @var string
   */
  
  private $errorDescription;

  public static function success(string $name, $transformed)
  {
    return new self($name, true, $transformed);
  }

  public static function failure(string $name, string $errorName, $errorDescription)
  {
    return new self($name, false, null, $errorName, $errorDescription);

  }

  private function __construct(
    string $name, 
    bool $valid, 
    $transformed = null, 
    string $errorName = null, 
    $errorDescription = null
  )
  {
    $this->name = $name;
    $this->valid = $valid;
    $this->transformed = $transformed;

    $this->errorName = $errorName;
    $this->errorDescription = $errorDescription;
  }

  public function isValid() : bool
  {
    return $this->valid;
  }

  public function getName() : string
  {
    return $this->name;
  }


  /**
   * This hook can be used to transform a given 
   * value while it is propagating through the
   * types.
   *
   * This method MUST not be called when execute failed.
   *
   * @param  mixed $value
   *    The input value
   *
   * @return mixed|null
   *    The output value
   */

  public function getTransformed()
  {
    return $this->transformed;
  }

  public function withTransformed($transformed)
  {
    if ($transformed === $this->transformed) {
      return $this;
    }

    $new = clone $this;
    $new->transformed = $transformed;

    return $new;
  }

  public function getErrorName() :? string
  {
    return $this->errorName;
  }

  /**
   * Returns the error message on a failed test
   * for the given name.
   *
   * This method MUST return an error message if the
   * last execution failed or if the type is in inital
   * state. If no error occured an empty string MUST be
   * returned.
   *
   *
   * @return  string
   *    The error message or empty string if no error occured
   */

  public function getErrorDescription() :? string
  {
    if ($this->isValid()) {
      return null;
    }

    if (is_string($this->errorDescription)) {
      return sprintf($this->errorDescription, $this->name);
    }

    if (is_callable($this->errorDescription)) {
      return call_user_func($this->errorDescription, $this->name);
    }

    throw new \InvalidArgumentException('Error message MUST be a string or callable.');
  }
}

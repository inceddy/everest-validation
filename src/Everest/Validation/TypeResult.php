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

  private $name;

  private $valid;

  private $transformed;

  private $error;

  public static function success(string $name, $transformed)
  {
    return new self($name, true, $transformed);
  }

  public static function failure(string $name, $error)
  {
    return new self($name, false, null, $error);
  }

  private function __construct(string $name, bool $valid, $transformed = null, $error = null)
  {
    $this->name;
    $this->valid = $valid;
    $this->transformed = $transformed;
    $this->error = $error;
  }

  public function isValid() : bool
  {
    return $this->valid;
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

  public function getErrorMessage() : string
  {
    if ($this->isValid()) {
      return '';
    }

    if (is_string($this->error)) {
      return sprintf($this->error, $this->name);
    }

    return ($this->error)($this->name);
  }
}

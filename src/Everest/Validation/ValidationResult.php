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

final class ValidationResult {

  private $success;

  private $failure;

  public function __construct(array $success, array $failure)
  {
    $this->success = $success;
    $this->failure = $failure;
  }

  public function isValid() : bool
  {
    return empty($this->failure);
  }

  public function errors() : array
  {
    return array_map(function($result) {
      return $result->getErrorMessage();
    }, $this->failure);
  }

  public function values() : array
  {
    return array_map(function($result){
      return $result->getTransformed();
    }, $this->success);
  }
}

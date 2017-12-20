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

  private $typeResults = [];

  public function addTypeResult(TypeResult $result)
  {
    $this->typeResults[$result->getName()] = $result;
    return $this;
  }

  public function getTypeResult(string $name)
  {
    return $this->typeResults[$name] ?? null;
  }

  public function isValid() : bool
  {
    foreach ($this->typeResults as $result) {
      if (!$result->isValid()) {
        return false;
      }
    }

    return true;
  }

  public function errors() : array
  {
    return array_map(function($result) {
      return [
        'name' => $result->getErrorName(),
        'description' => $result->getErrorDescription()

      ];
    }, array_filter($this->typeResults, function($result){
      return !$result->isValid();
    }));
  }

  public function values() : array
  {
    return array_map(function($result){
      return $result->getTransformed();
    }, array_filter($this->typeResults, function($result){
      return $result->isValid();
    }));
  }
}

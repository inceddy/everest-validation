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

class TypeStringRegEx extends TypeString {

  // Options
  public const RETURN_MATCHES = 8;

  /**
   * Pattern the string must match
   * @var string
   */
  
  private $pattern;

  /**
   * PregMatch flags
   * @var int
   */
  
  private $flags;

  /**
   * PregMatch offset
   * @var int
   */
  
  private $offset;

  /**
   * Constructor
   *
   * @see http://de1.php.net/manual/de/function.preg-match.php
   *
   * @param string $pattern
   *   The pattern the value must match
   * @param int $options
   *   String- and RegExpType options as bit-mask
   * @param int $flags
   *   PregMatch flags
   * @param int $offset
   *   PregMatch offset
   */
  
  public function __construct(string $pattern, int $options = 0, int $flags = 0, int $offset = 0) 
  {
    parent::__construct($options);

    $this->pattern = $pattern;
    $this->flags   = $flags;
    $this->offset  = $offset;
  }


  /**
   * {@inheritDoc}
   */

  public function execute(string $name, $value) : TypeResult
  {
    if (!($result = parent::execute($name, $value))->isValid()) {
      return $result;
    }

    $matches = [];
    $valid = 1 === preg_match(
      $this->pattern, 
      $result->getTransformed(), 
      $matches, 
      $this->flags, 
      $this->offset
    );
    
    return $valid ?
      ($this->options & self::RETURN_MATCHES ? $result->withTransformed($matches) : $result) :
      TypeResult::failure($name, function($name) {
        return sprintf('\'%s\' does not match pattern %s.', $name, $this->pattern);
      });
  }
}

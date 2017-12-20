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

class TypeFloatBetween extends TypeFloat {

	private $min;
	private $max;

	public function __construct(float $min = -INF, float $max = +INF) 
	{
		$this->min = $min;
		$this->max = $max;
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
			TypeResult::failure($name, 'float_between', function($name){
				return sprintf(
					'The value of \'%s\' is not between %s and %s.', 
					$name, $this->min, $this->max
				);
			});
	}
}

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
use Exception;

class TypeDateTime implements TypeInterface {

	private $format;

	private $targetClass;

	private $result;

	public function __construct(string $format = \DateTime::ATOM, string $targetClass = \DateTime::CLASS)
	{
		if (!is_subclass_of($targetClass, \DateTimeInterface::CLASS)) {
			throw new InvalidArgumentException('Target class MUST implement \\DateTimeInterface.');
		}

		$this->format = $format;
		$this->targetClass = $targetClass;
	}


	/**
	 * {@inheritDoc}
	 */

	public function execute(string $name, $value) : TypeResult
	{
		return (is_string($value) && $transformed = $this->targetClass::createFromFormat($this->format, $value)) ? 
			TypeResult::success($name, $transformed) :
			TypeResult::failure($name, function($name){
				return sprintf('\'%s\' is not a valid date/time of format %s', $name, $this->format);
			});
	}


	/**
	 * {@inheritDoc}
	 */

	public function getName() : string
	{
		return 'date_time';
	}
}

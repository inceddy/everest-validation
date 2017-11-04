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

class TypeString implements TypeInterface {

	// Options
	public const UPPER_CASE = 1;
	public const LOWER_CASE = 2;
	public const TRIM       = 4;


	/**
	 * Options bit-mask of this type
	 * @var int
	 */
	
	protected $options;

	public function __construct(int $options = self::TRIM) 
	{
		$this->options = $options;
	}


	/**
	 * {@inheritDoc}
	 */
	
	public function execute(string $name, $value) : TypeResult
	{
		if (!is_string($value)) {
			return TypeResult::failure($name, '\'%s\' is not a string.');
		}

		if ($this->options & self::TRIM) {
			$value = trim($value);
		}

		if ($this->options & self::LOWER_CASE) {
			$value = strtolower($value);
		}

		if ($this->options & self::UPPER_CASE) {
			$value = strtoupper($value);
		}

		return TypeResult::success($name, $value);
	}


	/**
	 * {@inheritDoc}
	 */

	public function getName() : string 
	{
		return 'string';
	}
}
<?php

/*
 * This file is part of Everest.
 *
 * (c) 2017 Philipp Steingrebe <development@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Everest\Validation\Expressions;

use Everest\Validation\TypeInterface;
use Everest\Validation\TypeResult;

class ExpressionOptional implements TypeInterface {

	public const TRIM   = 1;
	public const FILTER = 2;

	/**
	 * The type that is optional
	 * @var Everest\Validation\TypeInterface
	 */
	
	private $type;

	/**
	 * Option to trim strings (also in filter case)
	 * @var bool
	 */
	
	private $trim;

	/**
	 * Option to filter arrays
	 * @var bool
	 */
	
	private $filter;

	public function __construct(TypeInterface $type, int $options = 0) 
	{
		$this->type = $type;

		$this->trim   = (bool)($options & self::TRIM);
		$this->filter = (bool)($options & self::FILTER);
	}

	/**
	 * {@inheritDoc}
	 */

	public function execute(string $name, $value) : TypeResult
	{
		if (is_string($value) && $this->trim) {
			$value = trim($value);
		}

		else if (is_array($value) && $this->filter) {
			$value = array_filter($value, function($value){
				if (is_string($value) && $this->trim) {
					$value = trim($value);
				}

				return !empty($value);
			});
		}

		if (empty($value)) {
			return TypeResult::success($name, null);
		}

		return $this->type->execute($name, $value);
	}

	/**
	 * {@inheritDoc}
	 */

	public function getName() : string
	{
		return 'optional';
	}
}

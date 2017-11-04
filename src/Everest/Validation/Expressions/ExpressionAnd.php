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

class ExpressionAnd implements TypeInterface {

	private $types;

	private $failedTypes = [];

	public function __construct(TypeInterface ... $types) 
	{
		$this->types = $types;
	}


	/**
	 * {@inheritDoc}
	 */
	
	public function execute(string $name, $value) : TypeResult
	{
		$this->failedTypes = [];
		$success = true;

		foreach ($this->types as $type) {
			if (!($result = $type->execute($name, $value))->isValid()) {
				return $result;
			}

			$value = $result->getTransformed();
		}

		return $result;
	}


	/**
	 * {@inheritDoc}
	 */
	
	public function getName() : string
	{
		return 'and';
	}
}

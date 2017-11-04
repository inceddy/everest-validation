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

class ExpressionOr implements TypeInterface {

	private $types;

	public function __construct(TypeInterface ... $types) 
	{
		$this->types = $types;
	}


	/**
	 * {@inheritDoc}
	 */
	
	public function execute(string $name, $value) : TypeResult
	{
		foreach ($this->types as $type) {
			if (($result = $type->execute($name, $value))->isValid()) {
				return $result;
			}
		}

		return TypeResult::failure($name, function($name) {
			sprtinf(
				'\'%s\' statisfy one of the following types [%s].', 
				$name, 
				implode(', ', array_map(function($type){
					return $type->getName();
				}, $this->types))
			);
		});
	}


	/**
	 * {@inheritDoc}
	 */
	
	public function getName() : string
	{
		return 'or';
	}
}

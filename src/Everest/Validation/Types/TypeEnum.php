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

class TypeEnum extends TypeString {

	private $values;

	public function __construct(array $values, int $options = TypeString::TRIM) 
	{
		if (empty($values)) {
			throw new InvalidArgumentException('Values MUST NOT be empty.');
		}

		parent::__construct($options);

		$this->values = $values;
	}


	/**
	 * {@inheritDoc}
	 */

	public function execute(string $name, $value) : TypeResult
	{
		$result = parent::execute($name, $value);
		$value = $result->getTransformed();

		return array_key_exists($value, $this->values) ?
			TypeResult::success($name, $this->values[$value]) :
			TypeResult::failure($name, function($name) use ($value) {
				return sprintf(
					'\'%s[%s]\' does not exist in the enum. Use one of [%s]',
					$name, $value, implode(', ', array_keys($this->values))
				);
			});
	}

	public function getName() : string
	{
		return 'enum';
	}
}

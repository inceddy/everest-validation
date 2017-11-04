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

class TypeArray implements TypeInterface {

	public const FILTER = 1;

	private const FAIL_NOT_ARRAY  = 1;
	private const FAIL_WRONG_TYPE = 2;

	private $type;

	private $filter;

	public function __construct(TypeInterface $type, int $options = 0)
	{
		$this->type = $type;
		$this->filter = (bool)($options & self::FILTER);
	}

	/**
	 * Set reason state and return false
	 * @return bool
	 */
	
	private function fail(string $name, int $reason, ... $arguments) : TypeResult
	{
		return TypeResult::failure($name, function($name) use ($reason, $arguments){
			switch ($reason) {
				case self::FAIL_NOT_ARRAY:
					return sprintf('\'%s\' is not an array.', $name);

				case self::FAIL_WRONG_TYPE:
					return sprintf(
						'Wront type for \'%s[%s]\': %s.', 
						$name, $arguments[0], $arguments[1]->getErrorMessage()
					);
			}
		});
	}


	/**
	 * {@inheritDoc}
	 */

	public function execute(string $name, $values) : TypeResult
	{
		// Validate array
		if (!is_array($values)) {
			return $this->fail($name, self::FAIL_NOT_ARRAY);
		}

		$values = array_values($values);
		$transformed = [];

		if ($this->filter) {
			$values = array_filter($values);
		}

		// Validate type
		foreach ($values as $index => $value) {
			if (!($result = $this->type->execute($index, $value))->isValid()) {
				return $this->fail($name, self::FAIL_WRONG_TYPE, $index, $result);
			}

			$transformed[] = $result->getTransformed();
		}

		return TypeResult::success($name, $transformed);
	}

	public function getName() : string
	{
		return 'array';
	}
}

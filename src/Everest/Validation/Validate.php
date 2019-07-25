<?php

/*
 * This file is part of Everest.
 *
 * (c) 2018 Philipp Steingrebe <development@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Everest\Validation;

/**
 * Validates values in an array or arraylike object
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 * @package Everest\Validation
 */

final class Validate {

	/**
	 * Sets of validation chains to be executed on
	 * values in the data.
	 * @var array
	 */
	
	private $chainSets;

	/**
	 * The data to be validated
	 * @var array
	 */
	
	private $data;

	/**
	 * Flag if the validation is lazy or not
	 * @var boolean
	 */
	
	private $lazy;

	public static function lazy($data)
	{
		return new static($data, true);
	}

	public static function strict($data)
	{
		return new static($data, false);
	}

	private static function flatten(array $nextKeys, $data, array $prevKeys = [])
	{
		$currentKeys = [array_shift($nextKeys)];

		if (!$currentKeys[0] || $data instanceof Undefined) {
			return [implode('.', $prevKeys) => $data];
		}

		if (!is_array($data)) {
			return [implode('.', array_merge($currentKeys, $prevKeys)) => Undefined::instance()];
		}

		if ($currentKeys[0] === '*' || $currentKeys[0] === '') {
			$currentKeys = array_keys($data);
		}

		$results = [];

		foreach ($currentKeys as $key) {
			$results = array_merge($results, self::flatten(
				$nextKeys, 
				array_key_exists($key, $data) ? $data[$key] : Undefined::instance(),
				array_merge($prevKeys, [$key])
			));
		}

		return $results;
	}

	/**
	 * Taken from Laravel
	 *
	 * @param  array &$array
	 * @param  string|null $key
	 * @param  mixed $value
	 *
	 * @return array|mixed
	 */
	
	private static function expand(&$array, $key, $value)
	{
		if (is_null($key)) {
			return $array = $value;
		}

		$keys = explode('.', $key);

		while (count($keys) > 1) {
			$key = array_shift($keys);

			// If the key doesn't exist at this depth, we will just create an empty array
			// to hold the next value, allowing us to create the arrays to hold final
			// values at the correct depth. Then we'll keep digging into the array.
			if (!isset($array[$key]) || !is_array($array[$key])) {
				$array[$key] = [];
			}

			$array = &$array[$key];
		}

		$array[array_shift($keys)] = $value;

		return $array;
	}

	private function __construct($data, bool $lazy)
	{
		$this->setData($data);
		$this->setLazy($lazy);
	}

	public function setData($data)
	{
		if (is_object($data) && method_exists($data, 'toArray')) {
			$data = $data->toArray();
		}

		if (!(is_array($data) || $data instanceof \ArrayAccess)) {
			throw new \InvalidArgumentException(
				'Data to validate should be an array or implement the ArrayAccess interface.'
			);
		}

		$this->data = $data;
		return $this;
	}

	public function setLazy(bool $lazy)
	{
		$this->lazy = $lazy;
		return $this;
	}

	public function that(string $key, $message = null)
	{
		if (!isset($this->chainSets[$key])) {
			$this->chainSets[$key] = [];
		}

		$this->currentChain =
		$this->chainSets[$key][] = new ValidationChain($key, $message);

		return $this;
	}

	public function optional(...$args)
	{
		$this->currentChain->optional(...$args);
		return $this;
	}

	public function all()
	{
		$this->currentChain->all();
		return $this;
	}

	public function or($message = null)
	{
		return $this->that(
			$this->currentChain->getKey(),
			$message ?: $this->currentChain->getMessage()
		);
	}

	private function executeValidationChain(ValidationChain $chain, $value, string $primaryKey, array $nextKeys)
	{
		if ($chain->all) {
			$nextKeys[] = '*';
		}

		if (empty($nextKeys)) {
			try {
				return [$chain($value, $primaryKey), []];
			}
			catch (InvalidValidationException $error) {
				return [Undefined::instance(), [$error]];
			}
		}

		$errors = 
		$result = [];

		foreach (self::flatten($nextKeys, $value) as $index => $item) {
			try {
				$result[$index] = $chain->setKey($primaryKey . '.' . $index)($item, $index);
			}
			catch (InvalidValidationException $error) {
				$result[$index] = Undefined::instance();
				$errors[] = $error;
			}
		}

		$flatResult = [];

		foreach ($result as $key => $value) {
			self::expand($flatResult, $key, $value);
		}

		return [$flatResult, $errors];
	}

	private function executeStrict() : array
	{
		$result = [];

		foreach ($this->chainSets as $key => $chains) {
			$chainSetErrors = [];

			$nextKeys = explode('.', $key);
			$primaryKey = array_shift($nextKeys);

			$value = array_key_exists($primaryKey, $this->data) 
				? $this->data[$key] 
				: Undefined::instance();

			foreach ($chains as $chain) {
				[$chainResult, $chainErrors] = $this->executeValidationChain($chain, $value, $primaryKey, $nextKeys);

				if (empty($chainErrors)) {
					$chainSetErrors = [];
					if ($chainResult !== Undefined::instance()) {
						if (is_array($chainResult)) {
							$result[$primaryKey] = array_replace_recursive(
								$result[$primaryKey] ?? [],
								$chainResult
							);
						}
						else {
							$result[$primaryKey] = $chainResult;
						}
					}
					break;
				}

				$chainSetErrors = array_merge($chainSetErrors, $chainErrors);
			}

			// Stop on first error
			if (!empty($chainSetErrors)) {
				throw $chainSetErrors[0];
			}
		}

		return $result;
	}

	private function executeLazy() : array
	{
		$errors =
		$result = [];

		foreach ($this->chainSets as $key => $chains) {
			$chainSetErrors = [];
			$nextKeys = explode('.', $key);
			$primaryKey = array_shift($nextKeys);

			$value = array_key_exists($primaryKey, $this->data) 
				? $this->data[$primaryKey] 
				: Undefined::instance();


			foreach ($chains as $chain) {
				[$chainResult, $chainErrors] = $this->executeValidationChain($chain, $value, $primaryKey, $nextKeys);

				if (empty($chainErrors)) {
					$chainSetErrors = [];
					if ($chainResult !== Undefined::instance()) {
						if (is_array($chainResult)) {
							$result[$primaryKey] = array_replace_recursive(
								$result[$primaryKey] ?? [],
								$chainResult
							);
						}
						else {
							$result[$primaryKey] = $chainResult;
						}
					}
					break;
				}

				$chainSetErrors = array_merge($chainSetErrors, $chainErrors);
			}

			// Collect errors
			if (!empty($chainSetErrors)) {
				$errors = array_merge($errors, $chainSetErrors);
			}
		}

		if (!empty($errors)) {
			throw InvalidLazyValidationException::fromErrors($errors);
		}

		return $result;
	}

	public function execute($data = null)
	{
		if (null !== $data) {
			$this->setData($data);
		}

		return $this->lazy 
			? $this->executeLazy() 
			: $this->executeStrict();
	}

	public function __call($name, $args)
	{
		if (!$this->currentChain) {
			throw new \LogicException('No validation target specified yet');
		}

		$this->currentChain->add(function($value, string $key, $message = null) use ($args, $name) {
			array_unshift($args, $value);

			$argCount = count($args);
			$typeArgCount = Validation::getTypeParameterCount($name);

			if ($argCount == $typeArgCount - 2) {
				array_push($args, $message, $key);
			}
			else if ($argCount == $typeArgCount - 1) {
				array_push($args, $key);
			}

			return Validation::$name(...$args);
		});

		return $this;
	}
}

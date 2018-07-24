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
 * Validates alements in array
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 * @package Everest\Http
 */

final class Validate {

	private $chainSets;

	private $data;

	private $lazy;

	public static function lazy($data)
	{
		return new static($data, true);
	}

	public static function strict($data)
	{
		return new static($data, false);
	}

	private function __construct($data, bool $lazy)
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
		$this->lazy = $lazy;
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

	private function executeValidationChain(ValidationChain $chain, $value, $key = null)
	{
		if ($chain->all) {
			Validation::array($value);

			$errors = 
			$result = [];

			foreach ($value as $index => $item) {
				$error = null;
				foreach ($gen = $chain->setKey($key . '[' . $index . ']')($item, $index) as $error) {
					$errors[] = $error;
				}

				if (!$error) {
					$result[$index] = $gen->getReturn();
				}
			}

			return [$result, $errors];
		}

		$errors = [];

		foreach ($gen = $chain($value) as $error) {
			$errors[] = $error;
		}

		return [$gen->getReturn(), $errors];		
	}

	private function executeStrict() : array
	{
		$result = [];

		foreach ($this->chainSets as $key => $chains) {
			$chainSetErrors = [];

			$value = array_key_exists($key, $this->data) 
				? $this->data[$key] 
				: Undefined::instance();

			foreach ($chains as $chain) {
				[$chainResult, $chainErrors] = $this->executeValidationChain($chain, $value, $key);
				if (empty($chainErrors)) {
					if ($chainResult !== Undefined::instance()) {
						$result[$key] = $chainResult;
					}
					break;
				}

				$chainSetErrors = array_merge($chainSetErrors, $chainErrors);
			}

			if (!array_key_exists($key, $result)) {
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

			$value = array_key_exists($key, $this->data) 
				? $this->data[$key] 
				: Undefined::instance();

			foreach ($chains as $chain) {
				[$chainResult, $chainErrors] = $this->executeValidationChain($chain, $value, $key);
				if (empty($chainErrors)) {
					$chainSetErrors = [];
					if ($chainResult !== Undefined::instance()) {
						$result[$key] = $chainResult;
					}
					break;
				}

				$chainSetErrors = array_merge($chainSetErrors, $chainErrors);
			}

			if (!array_key_exists($key, $result)) {
				$errors = array_merge($errors, $chainSetErrors);
			}
		}

		if (!empty($errors)) {
			throw InvalidLazyValidationException::fromErrors($errors);
		}

		return $result;
	}

	public function execute()
	{
		return $this->lazy 
			? $this->executeLazy() 
			: $this->executeStrict();
	}

	public function __call($name, $args)
	{
		if (!$this->currentChain) {
			throw new \LogicException('No validation target specified yet.');
		}

		$this->currentChain->add(function($value, string $key, $message = null) use ($args, $name) {
			array_unshift($args, $value);
			array_push($args, $message, $key);

			return Validation::$name(...$args);
		});

		return $this;
	}
}

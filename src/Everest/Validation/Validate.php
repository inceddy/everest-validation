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

	public function optional($default = null)
	{
		$this->currentChain->optional($default);
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

	private function executeStrict() : array
	{
		$data = [];

		foreach ($this->chainSets as $key => $chains) {
			$errors = [];
			foreach ($chains as $chain) {
				$chainErrors = [];
				foreach ($gen = $chain($this->data[$key] ?? null) as $error) {
					$errors[] = $chainErrors[] = $error;
				}

				if (empty($chainErrors)) {
					$data[$key] = $gen->getReturn();
					continue 2;
				}
			}
			if ($errors) {
				throw $errors[0];
			}
		}

		return $data;
	}

	private function executeLazy() : array
	{
		$data = 
		$errors = [];

		foreach ($this->chainSets as $key => $chainSet) {
			$chainSetErrors = [];
			foreach ($chainSet as $chain) {
				$chainErrors = [];
				foreach ($gen = $chain($this->data[$key] ?? null) as $error) {
					$chainErrors[] = 
					$chainSetErrors[] = $error;
				}

				if (empty($chainErrors)) {
					unset($chainSetErrors);
					$data[$key] = $gen->getReturn();
					continue 2;
				}
			}
			if ($chainSetErrors) {

				$errors = array_merge($errors, $chainSetErrors);
			}
		}

		if ($errors) {
			throw InvalidLazyValidationException::fromErrors($errors);
		}

		return $data;
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

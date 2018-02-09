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
		$this->currentChain =
		$this->chains[$key] = new ValidationChain($key, $message);
		return $this;
	}

	public function thatMay(string $key, $message = null)
	{
		return $this->that($key, $message)->may();
	}

	public function may()
	{
		$this->currentChain->allowNull();
		return $this;
	}

	public function execute() : array
	{
		$data = 
		$errors = [];

		foreach ($this->chains as $key => $chain) {
			foreach ($gen = $chain($this->data[$key] ?? null) as $error) {
				if (!$this->lazy) {
					throw $error;
				}
				$errors[] = $error;
			}

			$data[$key] = $gen->getReturn();
		}

		if ($errors) {
			throw InvalidLazyValidationException::fromErrors($errors);
		}

		return $data;
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

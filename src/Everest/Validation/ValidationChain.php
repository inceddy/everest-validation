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
 * Validates multiple validations on supplied value
 * 
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 * @package Everest\Http
 */

final class ValidationChain {

	/**
	 * Stack of closures, that call the validions
	 * @var array
	 */
	
	private $validations = [];

	/**
	 * Indecates if null is a valid value
	 * @var boolean
	 */
	
	private $allowNull = false;

	/**
	 * Indecates if all validations should be
	 * performed after one already failed
	 * @var boolean
	 */
	
	private $all = false;

	/**
	 * The key of the value
	 * @var string
	 */
	
	private $key;

	/**
	 * Custom validation message
	 * @var string|callable|null
	 */
	
	private $message;

	/**
	 * @param string $key
	 * @param string|callable|null $message
	 */
	
	public function __construct(string $key, $message = null)
	{
		$this->key = $key;
		$this->message = $message;
	}

	/**
	 * Set all to true
	 */
	
	public function all()
	{
		$this->all = true;
		return $this;
	}

	/**
	 * Allow this chain to be
	 * true if value is `null`.
	 */
	
	public function allowNull()
	{
		$this->allowNull = true;
	}

	/**
	 * Adds a new validation clusore the the 
	 * validation chain.
	 *
	 * @param \Closure $validation
	 * 
	 * @return void
	 */
	
	public function add(\Closure $validation) : void
	{
		$this->validations[] = $validation;
	}

	/**
	 * Returns generator which trys to
	 * execute every validation in the given 
	 * value.
	 * Every exeption thrown by the validation
	 * are yielded.
	 * The final value of the chain is returned.
	 *
	 * @param  mixed $value
	 *   The value to validate
	 *
	 * @return mixed
	 *   The final value
	 */
	
	public function __invoke($value)
	{
		if (null === $value && $this->allowNull) {
			return null;
		}

		foreach ($this->validations as $validation) {
			try {
				$value = $validation($value, $this->key, $this->message);
			}
			catch (InvalidValidationException $e) {
				yield $e;
				if (!$this->all) {
					break;
				}
			}
		}

		return $value;
	}
}

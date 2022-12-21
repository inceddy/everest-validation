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

class ValidationChain {

	/**
	 * Stack of closures, that call the validions
	 * @var array
	 */
	
	private $validations = [];

	private $valid = true;

	/**
	 * Indecates if the chain is optional or not
	 * @var boolean
	 */
	
	private $optional = false;

	/**
	 * Indecates if the chain should only be executed when value is present
	 * @var        bool
	 */

	private $sometimes = false;

	/**
	 * Default value if the cain is optional
	 * If the default value is callable, it is called
	 * If the default value differs from null it is validated by the cain
	 * @var null
	 */
	
	private $default;

	/**
	 * Indecates if all validations should be
	 * performed after one already failed
	 * @var boolean
	 */
	
	public $all = false;

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
		$this->default = Undefined::instance();
		$this->key = $key;
		$this->message = $message;
	}

	public function getKey()
	{
		return $this->key;
	}

	public function setKey(string $key)
	{
		$this->key = $key;
		return $this;
	}

	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Input value is an array and
	 * this chain is executed on every element
	 * in the array.
	 */
	
	public function all()
	{
		$this->all = true;
	}

	/**
	 * Allow this chain to be
	 * true if value is equal to default value.
	 */
	
	public function optional()
	{
		if (func_num_args()) {
			$default = func_get_arg(0);

			if (is_callable($default)) {
				$default = call_user_func($default);
			}

			$this->default = $default;
		}

		$this->optional = true;
	}

	/**
	 * Allow this chain to be
	 * true if value is not present.
	 */
	public function sometimes()
	{
		$this->sometimes = true;
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
	
	public function __invoke($value, $index = null)
	{
		if ($value === Undefined::instance()) {
			if ($this->sometimes) {
				return $value;
			}

			if ($this->optional) {
				return $this->default;
			}

			throw new InvalidValidationException('missing', 'Required property is missing', $this->key, $value);
		}

		// If value is equal to default return it
		// This avoids chains like: `->that('foo')->optional(null)->string()->or()->null()		
		if ($this->optional && $this->default === $value) {
			return $value;
		}

		foreach ($this->validations as $validation) {
			$value = $validation($value, $this->key, $this->message);
		}

		return $value;
	}
}

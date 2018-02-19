<?php

class TypeTest extends Everest\Validation\Types\Type {

	private $factor;

	public function __construct(int $factor = 1)
	{
		$this->factor = $factor;
	}

	public function __invoke(int $value, $message = null, string $key = null)
	{
		return $value * $this->factor;
	}
}
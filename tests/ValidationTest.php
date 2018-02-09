<?php

use Everest\Validation\Validation;

/**
 * @author  Philipp Steingrebe <philipp@steingrebe.de>
 */

class ValidationTest extends \PHPUnit\Framework\TestCase {

	public function testTransfromOnly()
	{
		$value = Validation::TransformInteger('foo') ?:
						 Validation::TransformInteger([]) ?:
						 Validation::TransformInteger('45');
		$this->assertSame(45, $value);
	}
}

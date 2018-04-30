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

final class Undefined {
	private function __construct(){}

	public static function instance()
	{
		static $instance;

		if (!isset($instance)) {
			$instance = new self;
		}

		return $instance;
	}
} 
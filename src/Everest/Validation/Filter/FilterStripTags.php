<?php

/*
 * This file is part of Everest.
 *
 * (c) 2018 Philipp Steingrebe <development@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Everest\Validation\Filter;


class FilterStripTags
{
	public function __invoke(string $value, string $allowableTags = null)
	{
		return strip_tags($value, $allowableTags);
	}
}
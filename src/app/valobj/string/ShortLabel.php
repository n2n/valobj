<?php

namespace valobj\string;

use n2n\util\StringUtils;
use n2n\spec\valobj\err\IllegalValueException;

class ShortLabel extends CleanString {

	/**
	 * @param string $value that is clean according to {@link StringUtils::isClean} and max 31 chars long
	 * @throws IllegalValueException if passed value is invalid.
	 */
	const MAX_LENGTH = 31;
}
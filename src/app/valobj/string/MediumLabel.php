<?php
namespace valobj\string;

use n2n\spec\valobj\err\IllegalValueException;
use n2n\util\StringUtils;

class MediumLabel extends CleanString {
	/**
	 * @param string $value that is clean according to {@link StringUtils::isClean} and max 127 chars long
	 * @throws IllegalValueException if passed value is invalid.
	 */
	const MAX_LENGTH = 127;
	const MIN_LENGTH = 1;
	const SIMPLE_WHITESPACES_ONLY = true;
}
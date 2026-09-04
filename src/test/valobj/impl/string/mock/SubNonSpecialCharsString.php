<?php

namespace valobj\impl\string\mock;

use valobj\string\NonSpecialCharsString;

class SubNonSpecialCharsString extends NonSpecialCharsString {
	const LOWER_CASE_ONLY = false;
	const MIN_LENGTH = 8;
}
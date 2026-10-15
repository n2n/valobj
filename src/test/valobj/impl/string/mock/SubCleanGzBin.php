<?php

namespace valobj\impl\string\mock;

use valobj\string\CleanGzBin;

class SubCleanGzBin extends CleanGzBin {

	const MIN_LENGTH = 3;
	const MAX_LENGTH = 8;
	const SIMPLE_WHITESPACES_ONLY = true;

}
<?php

namespace valobj\string;

use n2n\util\StringUtils;
use n2n\spec\valobj\err\IllegalValueException;

class Name extends CleanString {

	const MAX_LENGTH = 80;
}
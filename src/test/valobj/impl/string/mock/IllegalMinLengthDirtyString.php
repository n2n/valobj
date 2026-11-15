<?php

namespace valobj\impl\string\mock;

use valobj\string\DirtyString;

class IllegalMinLengthDirtyString extends DirtyString {
	const MIN_LENGTH = 0;
}

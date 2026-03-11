<?php

namespace valobj\impl\string;

use PHPUnit\Framework\TestCase;
use valobj\string\CleanString;

class CleanStringTest extends TestCase {
	 function testFromTruncate() {
		 $this->assertEquals('This is a test string', CleanString::fromTruncate('This is a test string'));
		 $this->assertEquals(str_repeat('a', CleanString::MAX_LENGTH - 3)
				 . '...', CleanString::fromTruncate(str_repeat('a', CleanString::MAX_LENGTH + 1)));
	 }
}
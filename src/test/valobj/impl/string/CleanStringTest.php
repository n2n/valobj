<?php

namespace valobj\impl\string;

use PHPUnit\Framework\TestCase;
use valobj\string\CleanString;
use n2n\util\ex\IllegalStateException;

class CleanStringTest extends TestCase {
	function testFromTruncate() {
		$this->assertEquals('This is a test string', CleanString::fromTruncate('This is a test string'));
		$this->assertEquals(str_repeat('a', CleanString::MAX_LENGTH - 3)
			 . '...', CleanString::fromTruncate(str_repeat('a', CleanString::MAX_LENGTH + 1)));
	}

	function testFrom() {
		$this->assertEquals('This is a test string', CleanString::from('This is a test string'));
	}

	function testFromLenientFalseExpectException() {
		$this->expectException(IllegalStateException::class);
		CleanString::from(' This is a test string ');
	}

	function testFromLenient() {
		$this->assertEquals('This is a test string', CleanString::from(' This is a test string ', true));
	}
}
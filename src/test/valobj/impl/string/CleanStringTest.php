<?php

namespace valobj\impl\string;

use PHPUnit\Framework\TestCase;
use valobj\string\CleanString;
use n2n\util\ex\IllegalStateException;
use n2n\spec\valobj\err\IllegalValueException;

class CleanStringTest extends TestCase {
	function testFromTruncate() {
		$this->assertEquals('This is a test string', CleanString::fromTruncate('This is a test string'));
		$this->assertEquals(str_repeat('a', CleanString::MAX_LENGTH - 3)
			 . '...', CleanString::fromTruncate(str_repeat('a', CleanString::MAX_LENGTH + 1)));
	}

	function testFromLenientFalse() {
		$this->assertNull(CleanString::from(null));
		$this->assertEquals('This is a test string', CleanString::from('This is a test string'));
	}

	function testFromLenientFalseExpectException() {
		$this->expectException(IllegalStateException::class);
		CleanString::from(' This is a test string ');
	}

	function testFromLenient() {
		$this->assertEquals('This is a test string', CleanString::from(' This is a test string ', true));
	}

	/**
	 * @throws IllegalValueException
	 */
	function testCheckedFromLenient() {
		$this->assertEquals('This is a test string', CleanString::checkedFrom(' This is a test string ', true));
		$this->assertNull(CleanString::checkedFrom('   ', true));
		$this->assertNull(CleanString::from('   ', true));
	}

	function testCheckedFromLenientFalseExpectException() {
		$this->expectException(IllegalValueException::class);
		CleanString::checkedFrom(' This is a test string ');
	}
}

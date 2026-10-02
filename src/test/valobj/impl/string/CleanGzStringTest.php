<?php

namespace valobj\impl\string;

use PHPUnit\Framework\TestCase;
use valobj\string\CleanGzString;
use n2n\util\ex\IllegalStateException;

class CleanGzStringTest extends TestCase {
	function testfromUncompressed() {
		$this->assertEquals(hex2bin('78da0bc9c82c5600a2448592d4e21285e292a2ccbc7400514907ad'),
				CleanGzString::fromUncompressed('This is a test string'));


	}

	function testfromCompressed() {
		$this->assertEquals('This is a test string',
				CleanGzString::fromUncompressed('This is a test string')->uncompress());


	}

	function testFromLenientFalse() {
		$this->assertNull(CleanGzString::from(null));
		$this->assertEquals(hex2bin('78da0bc9c82c5600a2448592d4e21285e292a2ccbc7400514907ad'),
				CleanGzString::from(hex2bin('78da0bc9c82c5600a2448592d4e21285e292a2ccbc7400514907ad')));
	}

	function testFromLenientFalseExpectException() {
		$this->expectException(IllegalStateException::class);
		CleanGzString::from(' This is a test string ');
	}

}
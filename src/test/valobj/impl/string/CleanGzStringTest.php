<?php

namespace valobj\impl\string;

use PHPUnit\Framework\TestCase;
use valobj\string\CleanGzString;
use n2n\util\ex\IllegalStateException;
use n2n\spec\valobj\err\IllegalValueException;
use valobj\impl\string\mock\SubCleanGzString;

class CleanGzStringTest extends TestCase {
	function testFromUncompressed() {
		$this->assertEquals(hex2bin('78da0bc9c82c5600a2448592d4e21285e292a2ccbc7400514907ad'),
				CleanGzString::fromUncompressed('This is a test string'));
		$this->assertEquals(hex2bin('78da2b492d2e0100045d01c1'),
				SubCleanGzString::fromUncompressed('test'));
	}


	/**
	 * @throws IllegalValueException
	 */
	function testFromCompressed() {
		$this->assertEquals('This is a test string',
				(new CleanGzString(hex2bin('78da0bc9c82c5600a2448592d4e21285e292a2ccbc7400514907ad')))->uncompress());
	}

	/**
	 * @throws IllegalValueException
	 */
	function testHex2BinCleanGzString() {
		$this->assertNull(CleanGzString::from(null));
		$this->assertEquals(hex2bin('78da0bc9c82c5600a2448592d4e21285e292a2ccbc7400514907ad'),
				new CleanGzString(hex2bin('78da0bc9c82c5600a2448592d4e21285e292a2ccbc7400514907ad')));
	}

	function testSubCleanGzStringMinMaxAndWhiteSpaceAllowedWhichWillFailWithSubCleanGzString() {
		$this->assertNotNull(CleanGzString::fromUncompressed(' T '));
		$this->assertNotNull(CleanGzString::fromUncompressed('T'));
		$this->assertNotNull(CleanGzString::fromUncompressed(' This is a test string '));
	}

	function testSubCleanGzStringExpectExceptionBecauseTooLong() {
		$this->expectException(IllegalStateException::class);
		$this->expectExceptionMessage('Value too long:');
		SubCleanGzString::fromUncompressed(' This is a test string ');
	}

	function testSubCleanGzStringExpectExceptionBecauseTooShort() {
		$this->expectException(IllegalStateException::class);
		$this->expectExceptionMessage('Value too short:');
		SubCleanGzString::fromUncompressed('T');
	}

	function testSubCleanGzStringExpectExceptionBecauseNotClean() {
		// unchecked exception expected.
		$this->expectException(IllegalStateException::class);
		$this->expectExceptionMessage('Value not clean:');
		SubCleanGzString::fromUncompressed(' T ');
	}

	function testSubCleanGzStringExpectExceptionBecauseNotClean2() {
		// checked exception expected.
		$this->expectException(IllegalValueException::class);
		$this->expectExceptionMessage('Value not clean:');
		SubCleanGzString::checkedFromUncompressed(' T ');
	}

	function testSubCleanGzStringExpectExceptionBecauseCleanedLenientTooShort() {
		$this->expectException(IllegalValueException::class);
		$this->expectExceptionMessage('Value too short:');
		SubCleanGzString::checkedFromUncompressed(' T ', true);
	}

	function testSubCleanGzStringExpectExceptionBecauseCleanedLenientTooShort2() {
		// trim string and afterwards remove undisplayable characters (after lenient).
		$gzString = SubCleanGzString::fromUncompressed('  t‌e‌s‌t  ', true);
		$this->assertEquals(hex2bin('78da2b492d2e0100045d01c1'), $gzString);
		$this->assertEquals('test', $gzString->uncompress());
	}

	function testSubCleanGzStringExpectExceptionBecauseNotGzCompressed() {
		$this->expectException(IllegalValueException::class);
		$this->expectExceptionMessage('Value is no GZ String:');
		new SubCleanGzString('ö');
	}

}
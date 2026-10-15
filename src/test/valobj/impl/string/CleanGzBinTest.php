<?php

namespace valobj\impl\string;

use PHPUnit\Framework\TestCase;
use valobj\string\CleanGzBin;
use n2n\util\ex\IllegalStateException;
use n2n\spec\valobj\err\IllegalValueException;
use valobj\impl\string\mock\SubCleanGzBin;

class CleanGzBinTest extends TestCase {
	function testFromUncompressed() {
		$this->assertEquals(gzcompress( 'This is a test string', 9),
				CleanGzBin::fromUncompressed('This is a test string'));
		$this->assertEquals(gzcompress( 'test', 9),
				SubCleanGzBin::fromUncompressed('test'));
	}


	/**
	 * @throws IllegalValueException
	 */
	function testUncompressed() {
		$this->assertEquals('This is a test string',
				(new CleanGzBin(gzcompress( 'This is a test string', 9)))->uncompress());
	}

	/**
	 * @throws IllegalValueException
	 */
	function testFrom() {
		$this->assertNull(CleanGzBin::from(null));
		$this->assertNull(CleanGzBin::checkedFromUncompressed('', true));
		$this->assertNull(CleanGzBin::fromUncompressed(null));
		$this->assertEquals('This is a test string',
				CleanGzBin::checkedFrom(gzcompress('This is a test string', 9))->uncompress());
		$this->assertEquals(gzcompress( 'This is a test string', 9),
				new CleanGzBin(gzcompress( 'This is a test string', 9)));
	}

	function testSubCleanGzStringMinMaxAndWhiteSpaceAllowedWhichWillFailWithSubCleanGzString() {
		$this->assertNotNull(CleanGzBin::fromUncompressed(' T '));
		$this->assertNotNull(CleanGzBin::fromUncompressed('T'));
		$this->assertNotNull(CleanGzBin::fromUncompressed(' This is a test string '));
	}

	function testSubCleanGzStringExpectExceptionBecauseTooLong() {
		$this->expectException(IllegalStateException::class);
		$this->expectExceptionMessage('Uncompressed value too long:');
		SubCleanGzBin::fromUncompressed(' This is a test string ');
	}

	function testSubCleanGzStringExpectExceptionBecauseTooShort() {
		$this->expectException(IllegalStateException::class);
		$this->expectExceptionMessage('Uncompressed value too short:');
		SubCleanGzBin::fromUncompressed('T');
	}

	function testSubCleanGzStringExpectExceptionBecauseNotClean() {
		// unchecked exception expected.
		$this->expectException(IllegalStateException::class);
		$this->expectExceptionMessage('Uncompressed value not clean:');
		SubCleanGzBin::fromUncompressed(' T ');
	}

	function testSubCleanGzStringExpectExceptionBecauseNotClean2() {
		// checked exception expected.
		$this->expectException(IllegalValueException::class);
		$this->expectExceptionMessage('Uncompressed value not clean:');
		SubCleanGzBin::checkedFromUncompressed(' T ');
	}

	function testSubCleanGzStringExpectExceptionBecauseCleanedLenientTooShort() {
		$this->expectException(IllegalValueException::class);
		$this->expectExceptionMessage('Uncompressed value too short:');
		SubCleanGzBin::checkedFromUncompressed(' T ', true);
	}

	function testSubCleanGzStringExpectExceptionBecauseCleanedLenientTooShort2() {
		// trim string and afterwards remove undisplayable characters (after lenient).
		$gzString = SubCleanGzBin::fromUncompressed('  t‌e‌s‌t  ', true);
		$this->assertEquals(gzcompress( 'test', 9), $gzString);
		$this->assertEquals('test', $gzString->uncompress());
	}

	function testSubCleanGzStringExpectExceptionBecauseNotGzCompressed() {
		$this->expectException(IllegalValueException::class);
		$this->expectExceptionMessage('Value is no gnu zipped string:');
		new SubCleanGzBin('ö');
	}

}
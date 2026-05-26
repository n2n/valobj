<?php

namespace valobj\impl\string;

use PHPUnit\Framework\TestCase;
use n2n\util\ex\IllegalStateException;
use valobj\string\Phone;

class PhoneTest extends TestCase {
	function testLenientFrom(): void {
		$this->assertSame('+49 (0)228-997799-0', Phone::from('+49 (0)228-997799-0', true)->toScalar());
		$this->assertSame('+49 (0)228-997799-0', Phone::from(' +49 (0)228-997799-0 ', true)->toScalar());
		$this->assertSame('+49 (0)228-997799-0', Phone::from('0049 (0)228-997799-0', true)->toScalar());
		$this->assertSame('+49 228-997799-0', Phone::from('0049 228-997799-0', true)->toScalar());
		$this->assertSame('0228-997799-0', Phone::from('0228-997799-0', true)->toScalar());
		$this->assertSame('+49(0)228-997799-0', Phone::from('+49(0)228-997799-0', true)->toScalar());
		$this->assertSame('+900 (0)228-997799-0', Phone::from('+900 (0)228-997799-0', true)->toScalar());
		$this->assertSame('+900(0)228-997799-0', Phone::from('+900(0)228-997799-0', true)->toScalar());
		$this->assertSame('+41(0)79 123 45 67', Phone::from('+41(0)79 123 45 67', true)->toScalar());
		$this->assertSame('079 1 2 3 45 67', Phone::from('  079  1 2 3  45  67  ', true)->toScalar());
		$this->assertSame('0228-997799-0', Phone::from('(0)228-997799-0', true)->toScalar());
		$this->assertSame('+49 10228-997799-0', Phone::from('+49 (1)0228-997799-0', true)->toScalar());
		$this->assertSame('+900 1228-0997799-0', Phone::from('+900 1228-(0)997799-0', true)->toScalar());
		$this->assertEquals('+900(0)122899977990', Phone::from('+900(0)122899977990', true));

		$this->assertSame('+12340791234567', Phone::from('+1234(0)791234567', true)->toScalar());
		$this->assertSame('+0791234567', Phone::from('+ (0)791234567', true)->toScalar());

	}

	function testToTel(): void {
		$this->assertEquals('+492289977990', Phone::from('+49 (0)228-997799-0', true)->toTel());
	}

	function testFailPhoneBecausePlusOnWrongPlace(): void {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('0228997799-0', Phone::from('0228+997799-0', true));
	}

	function testFailPhoneBecauseIllegalChars(): void {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('+49 1228-997799-0', Phone::from(' tel: +49 228-997799-0 ', true));
	}


	function testFailPhoneBecauseToLong(): void {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('+9001228099977990', Phone::from('+9001228099977990', true));
	}

	function testNotLenientFrom(): void {
		// phone expect normalized number, that means +49 instead of 0049 when used without lenient
		$this->expectException(IllegalStateException::class);
		Phone::from('0049 (0)228-997799-0', false);
	}

	function testNotLenientFrom2(): void {
		// phone expect normalized number, that means more digits than whitespaces
		$this->expectException(IllegalStateException::class);
		Phone::from('  079  1 2 3  45  67  ', false);
	}
}
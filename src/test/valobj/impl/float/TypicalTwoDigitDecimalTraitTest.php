<?php

namespace valobj\impl\float;

use PHPUnit\Framework\TestCase;
use n2n\bind\err\BindMismatchException;
use n2n\bind\err\UnresolvableBindableException;
use n2n\bind\build\impl\Bind;
use n2n\bind\mapper\impl\Mappers;
use n2n\spec\valobj\err\IllegalValueException;

class TypicalTwoDigitDecimalTraitTest extends TestCase {

	/**
	 * @throws IllegalValueException
	 */
	function testConstruct(): void {
		$valObj = new SubTypicalTwoDigitDecimal(99999.99);
		$this->assertSame(99999.99, $valObj->toScalar());
	}

	function testConstructExceptionBecauseToSmall(): void {
		$this->expectException(IllegalValueException::class);
		$valObj = new SubTypicalTwoDigitDecimal(-100001.01);
	}

	function testConstructExceptionBecauseToBig(): void {
		$this->expectException(IllegalValueException::class);
		$valObj = new SubTypicalTwoDigitDecimal(100000.01);
	}

	/**
	 * @throws IllegalValueException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testMarshal(): void {
		$result = Bind::values(new SubTypicalTwoDigitDecimal(1.11), null)
				->map(Mappers::marshal())
				->toValue()
				->exec();

		$this->assertSame(1.11, $result->get()[0]);
		$this->assertNull($result->get()[1]);
	}

	/**
	 * @throws IllegalValueException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshal(): void {
		$result = Bind::values(1.11, null)
				->map(Mappers::unmarshal(SubTypicalTwoDigitDecimal::class))
				->toValue()
				->exec();

		$this->assertEquals(new SubTypicalTwoDigitDecimal(1.11), $result->get()[0]);
		$this->assertNull($result->get()[1]);
	}
}
<?php

namespace valobj\impl\string;

use PHPUnit\Framework\TestCase;
use valobj\ValueObjects;
use n2n\util\ex\IllegalStateException;
use n2n\bind\err\BindTargetException;
use n2n\bind\err\BindMismatchException;
use n2n\bind\err\UnresolvableBindableException;
use n2n\bind\build\impl\Bind;
use n2n\bind\mapper\impl\Mappers;
use n2n\validation\plan\ErrorMap;
use valobj\string\ShortLabel;
use n2n\spec\valobj\err\IllegalValueException;
use valobj\impl\int\mock\SubNbId;
use valobj\impl\string\mock\SubShortLabel;

class ShortLabelTest extends TestCase {


	function testConstruct(): void {
		$shortLabel = ValueObjects::shortLabel('Testerich');
		$this->assertEquals('Testerich', $shortLabel->toScalar());

		//as long only visible chars are used (and maybe spaces between) almost anything is possible even emojis
		$shortLabel = ValueObjects::shortLabel('🔧N2N-Works🔧');
		$this->assertEquals('🔧N2N-Works🔧', $shortLabel->toScalar());
	}

	function testConstructExceptionBecauseToLong(): void {
		$this->expectException(IllegalStateException::class);
		ValueObjects::shortLabel(str_repeat('s', 32));
	}

	function testShortLabelValueObjectExpectExceptionBecauseNotClean() {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('name',
				ValueObjects::shortLabel(' ​äüö‍‍‍àéè+‌"*ç%‎‏&/'));
	}

	function testShortLabelValueObjectExpectExceptionBecauseLeadingSpaceIsNotClean() {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('name', ValueObjects::shortLabel(' asdf'));
	}

	function testShortLabelValueObjectExpectExceptionBecauseTrailingSpaceIsNotClean() {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('name', ValueObjects::shortLabel('asdf '));
	}

	/**
	 * @throws BindTargetException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshal(): void {
		$result = Bind::values('Testerich', null)
				->map(Mappers::unmarshal(ShortLabel::class))
				->toValue()
				->exec();

		$this->assertEquals(new ShortLabel('Testerich'), $result->get()[0]);
		$this->assertNull($result->get()[1]);
	}

	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testUnmarshalValFail(): void {
		$result = Bind::values(str_repeat('s', 32))
				->map(Mappers::unmarshal(ShortLabel::class))
				->toValue()
				->exec();

		$this->assertFalse($result->isValid());
		$errorMap = $result->getErrorMap();
		$this->assertTrue(assert($errorMap instanceof ErrorMap));
		$this->assertEquals('Maxlength [maxlength = 31]', (string) $errorMap->getAllMessages()[0]);
	}


	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testMarshal(): void {
		$result = Bind::values(new ShortLabel('Testerich'), null)
				->map(Mappers::marshal())
				->toValue()
				->exec();

		$this->assertEquals('Testerich', $result->get()[0]);
		$this->assertNull($result->get()[1]);
	}

	/**
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshalSubclass(): void {
		$result = Bind::values('very-short')
				->map(Mappers::unmarshal(SubShortLabel::class))
				->toValue()
				->exec();

		$subShortLabel = $result->get();
		$this->assertInstanceOf(SubShortLabel::class, $subShortLabel);
		$this->assertEquals(new SubShortLabel('very-short'), $subShortLabel);
	}
}
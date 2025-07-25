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
use valobj\string\LongLabel;

class LongLabelTest extends TestCase {


	function testConstruct(): void {
		$longLabel = ValueObjects::longLabel('Testerich');
		$this->assertEquals('Testerich', $longLabel->toScalar());

		//as long only visible chars are used (and maybe spaces between) almost anything is possible even emojis
		$longLabel = ValueObjects::longLabel('🔧N2N-Works🔧');
		$this->assertEquals('🔧N2N-Works🔧', $longLabel->toScalar());
	}

	function testConstructExceptionBecauseToLong(): void {
		$this->expectException(IllegalStateException::class);
		ValueObjects::longLabel(str_repeat('s', 256));
	}

	function testLongLabelValueObjectExpectExceptionBecauseNotClean() {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('name',
				ValueObjects::longLabel(' ​äüö‍‍‍àéè+‌"*ç%‎‏&/'));
	}

	function testLongLabelValueObjectExpectExceptionBecauseLeadingSpaceIsNotClean() {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('name', ValueObjects::longLabel(' asdf'));
	}

	function testLongLabelValueObjectExpectExceptionBecauseTrailingSpaceIsNotClean() {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('name', ValueObjects::longLabel('asdf '));
	}

	/**
	 * @throws BindTargetException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshal(): void {
		$result = Bind::values('Testerich', null)
				->map(Mappers::unmarshal(LongLabel::class))
				->toValue()
				->exec();

		$this->assertEquals(new LongLabel('Testerich'), $result->get()[0]);
		$this->assertNull($result->get()[1]);
	}

	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testUnmarshalValFail(): void {
		$result = Bind::values(str_repeat('s', 256))
				->map(Mappers::unmarshal(LongLabel::class))
				->toValue()
				->exec();

		$this->assertFalse($result->isValid());
		$errorMap = $result->getErrorMap();
		$this->assertTrue(assert($errorMap instanceof ErrorMap));
		$this->assertEquals('Maxlength [maxlength = 255]', (string) $errorMap->getAllMessages()[0]);
	}


	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testMarshal(): void {
		$result = Bind::values(new LongLabel('Testerich'), null)
				->map(Mappers::marshal())
				->toValue()
				->exec();

		$this->assertEquals('Testerich', $result->get()[0]);
		$this->assertNull($result->get()[1]);
	}
}
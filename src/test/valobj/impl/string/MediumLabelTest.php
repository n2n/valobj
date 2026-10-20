<?php

namespace valobj\impl\string;

use PHPUnit\Framework\TestCase;
use n2n\util\ex\IllegalStateException;
use n2n\bind\err\BindTargetException;
use n2n\bind\err\BindMismatchException;
use n2n\bind\err\UnresolvableBindableException;
use n2n\bind\build\impl\Bind;
use n2n\bind\mapper\impl\Mappers;
use n2n\validation\plan\ErrorMap;
use valobj\string\MediumLabel;
use valobj\impl\string\mock\SubMediumLabel;

class MediumLabelTest extends TestCase {


	function testConstruct(): void {
		$MediumLabel = MediumLabel::from('Testerich');
		$this->assertEquals('Testerich', $MediumLabel->toScalar());

		//as long only visible chars are used (and maybe spaces between) almost anything is possible even emojis
		$MediumLabel = MediumLabel::from('🔧N2N-Works🔧');
		$this->assertEquals('🔧N2N-Works🔧', $MediumLabel->toScalar());
	}

	function testConstructExceptionBecauseToLong(): void {
		$this->expectException(IllegalStateException::class);
		MediumLabel::from(str_repeat('s', 128));
	}

	function testMediumLabelValueObjectExpectExceptionBecauseNotClean() {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('name',
				MediumLabel::from(' ​äüö‍‍‍àéè+‌"*ç%‎‏&/'));
	}

	function testMediumLabelValueObjectExpectExceptionBecauseLeadingSpaceIsNotClean() {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('name', MediumLabel::from(' asdf'));
	}

	function testMediumLabelValueObjectExpectExceptionBecauseTrailingSpaceIsNotClean() {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('name', MediumLabel::from('asdf '));
	}

	/**
	 * @throws BindTargetException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshal(): void {
		$result = Bind::values('Testerich', null)
				->map(Mappers::unmarshal(MediumLabel::class))
				->toValue()
				->exec();

		$this->assertEquals(new MediumLabel('Testerich'), $result->get()[0]);
		$this->assertNull($result->get()[1]);
	}

	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testUnmarshalValFail(): void {
		$result = Bind::values(str_repeat('s', 128))
				->map(Mappers::unmarshal(MediumLabel::class))
				->toValue()
				->exec();

		$this->assertFalse($result->isValid());
		$errorMap = $result->getErrorMap();
		$this->assertTrue(assert($errorMap instanceof ErrorMap));
		$this->assertEquals('Maxlength [maxlength = 127]', (string) $errorMap->getAllMessages()[0]);
	}


	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testMarshal(): void {
		$result = Bind::values(new MediumLabel('Testerich'), null)
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
				->map(Mappers::unmarshal(SubMediumLabel::class))
				->toValue()
				->exec();

		$subMediumLabel = $result->get();
		$this->assertInstanceOf(SubMediumLabel::class, $subMediumLabel);
		$this->assertEquals(new SubMediumLabel('very-short'), $subMediumLabel);
	}
}
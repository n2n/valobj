<?php

namespace valobj\impl\string;

use valobj\string\NonSpecialCharsString;
use PHPUnit\Framework\TestCase;
use n2n\spec\valobj\err\IllegalValueException;
use n2n\bind\err\BindTargetException;
use n2n\bind\err\BindMismatchException;
use n2n\bind\err\UnresolvableBindableException;
use n2n\bind\build\impl\Bind;
use n2n\bind\mapper\impl\Mappers;
use n2n\validation\plan\ErrorMap;
use valobj\impl\string\mock\SubNonSpecialCharsString;
use n2n\util\magic\MagicContext;
use n2n\test\case\N2nTestCaseTrait;

class NonSpecialCharsStringTest extends TestCase {
	use N2nTestCaseTrait;

	function testConstruct(): void {
		$pathPart = new NonSpecialCharsString('asdf');
		$this->assertEquals('asdf', $pathPart->toScalar());
	}

	function testConstructSubThatHasNoLowercaseRestriction(): void {
		$pathPart = new SubNonSpecialCharsString('AsDfAsDf');
		$this->assertEquals('AsDfAsDf', $pathPart->toScalar());
	}

	function testConstructExceptionBecauseToShort(): void {
		$this->expectException(IllegalValueException::class);
		$this->expectExceptionMessage('Value too short: ');
		new NonSpecialCharsString('ha');
	}

	function testConstructExceptionBecauseToLong(): void {
		$this->expectException(IllegalValueException::class);
		$this->expectExceptionMessage('Value too long: ');
		new NonSpecialCharsString('hadschi-rafiq-tariq-ben-hadschi-nabil-kamal-ibn-hadschi-faris-al-farouk');
	}

	function testNonSpecialCharsStringExpectExceptionBecauseNotClean() {
		$this->expectException(IllegalValueException::class);
		$this->expectExceptionMessage('Value contains special chars:');
		new NonSpecialCharsString('Hadschi&Rafiq');
	}

	function testNonSpecialCharsStringExpectExceptionBecauseNotLowerCase() {
		$this->expectException(IllegalValueException::class);
		$this->expectExceptionMessage('Value not lowercase:');
		new NonSpecialCharsString('HadschiRafiq');
	}

	/**
	 * @throws BindTargetException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshal(): void {
		$result = Bind::values('Hadschi-Rafiq', null)
				->map(Mappers::unmarshal(NonSpecialCharsString::class))
				->toValue()
				->exec();
		$this->assertEquals(new NonSpecialCharsString('hadschi-rafiq'), $result->get()[0]);
		$this->assertNull($result->get()[1]);
	}

	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testUnmarshalValFail(): void {
		$result = Bind::values('hadschi-rafiq-tariq-ben-hadschi-nabil-kamal-ibn-hadschi-faris-al-farouk')
				->map(Mappers::unmarshal(NonSpecialCharsString::class))
				->toValue()
				->exec();

		$this->assertFalse($result->isValid());
		$errorMap = $result->getErrorMap();
		$this->assertTrue(assert($errorMap instanceof ErrorMap));
		$this->assertEquals('Maxlength [maxlength = 63]', (string) $errorMap->getAllMessages()[0]);
	}

	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testMarshal(): void {
		$result = Bind::values(new NonSpecialCharsString('testerich'), null)
				->map(Mappers::marshal())
				->toValue()
				->exec();

		$this->assertEquals('testerich', $result->get()[0]);
		$this->assertNull($result->get()[1]);
	}

	/**
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshalSubclass(): void {
		$result = Bind::values('myStreetIsSuper&Duper', 'myStreet')
				->map(Mappers::unmarshal(SubNonSpecialCharsString::class))
				->toValue()
				->exec($this->getMockBuilder(MagicContext::class)->getMock());

		$subName = $result->get();
		$this->assertContainsOnlyInstancesOf(SubNonSpecialCharsString::class, $subName);
		$this->assertTypeSafeEquals(new SubNonSpecialCharsString('myStreetIsSuperDuper'), $subName[0]);
		$this->assertTypeSafeEquals(new SubNonSpecialCharsString('myStreet'), $subName[1]);
	}


}

<?php

namespace valobj\impl\string;

use PHPUnit\Framework\TestCase;
use valobj\ValueObjects;
use n2n\util\ex\IllegalStateException;
use valobj\string\LatinString;
use n2n\bind\err\BindTargetException;
use n2n\bind\err\BindMismatchException;
use n2n\bind\err\UnresolvableBindableException;
use n2n\bind\build\impl\Bind;
use n2n\bind\mapper\impl\Mappers;
use n2n\validation\plan\ErrorMap;
use valobj\impl\string\mock\SubLatinString;

class LatinStringTest extends TestCase {


	function testConstruct(): void {
		$latinString = ValueObjects::latinString('Testerich');
		$this->assertTrue($latinString instanceof LatinString);
		$this->assertEquals('Testerich', $latinString->toScalar());

		$latinString = ValueObjects::latinString('äüöàéèåçÿßœ');
		$this->assertEquals('äüöàéèåçÿßœ', $latinString->toScalar());
	}

	function testConstructExceptionBecauseToLong(): void {
		$this->expectException(IllegalStateException::class);
		//an arabic latinString that is way too long, because it includes father, grandfather and epithet
		ValueObjects::latinString('Hadschi Rafiq Tariq Ben Hadschi Nabil Kamal Ibn Hadschi Faris al Farouk');
	}

	function testLatinStringValueObjectExpectExceptionBecauseNotClean() {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('latinString',
				ValueObjects::latinString(' ​äüö‍‍‍àéè+‌"*ç%‎‏&/'));
	}

	function testLatinStringValueObjectExpectExceptionBecauseLeadingSpaceIsNotClean() {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('latinString', ValueObjects::latinString(' asdf'));
	}

	function testLatinStringValueObjectExpectExceptionBecauseTrailingSpaceIsNotClean() {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('latinString', ValueObjects::latinString('asdf '));
	}

	/**
	 * @throws BindTargetException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshal(): void {
		$result = Bind::values('Testerich', null)
				->map(Mappers::unmarshal(LatinString::class))
				->toValue()
				->exec();

		$this->assertEquals(new LatinString('Testerich'), $result->get()[0]);
		$this->assertNull($result->get()[1]);
	}

	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testUnmarshalValFail(): void {
		$result = Bind::values('Hadschi Rafiq Tariq Ben Hadschi Nabil Kamal Ibn Hadschi Faris al Farouk')
				->map(Mappers::unmarshal(LatinString::class))
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
		$result = Bind::values(new LatinString('Testerich'), null)
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
		$result = Bind::values('mylatinString')
				->map(Mappers::unmarshal(SubLatinString::class))
				->toValue()
				->exec();

		$subLatinString = $result->get();
		$this->assertInstanceOf(SubLatinString::class, $subLatinString);
		$this->assertEquals(new SubLatinString('mylatinString'), $subLatinString);
	}

	/**
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshalSubclassExpectBindError(): void {
		$result = Bind::values('mylatinStringToLong', 's')
				->map(Mappers::unmarshal(SubLatinString::class))
				->toValue()
				->exec();

		$this->assertFalse($result->isValid());
		$errorMap = $result->getErrorMap();
		$this->assertTrue(assert($errorMap instanceof ErrorMap));
		$this->assertEquals('Maxlength [maxlength = 15]', (string) $errorMap->getAllMessages()[0]);
		$this->assertEquals('Minlength [minlength = 2]', (string) $errorMap->getAllMessages()[1]);
	}
}
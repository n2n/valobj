<?php

namespace valobj\impl\string;

use PHPUnit\Framework\TestCase;
use valobj\ValueObjects;
use n2n\util\ex\IllegalStateException;
use valobj\string\Name;
use n2n\bind\err\BindTargetException;
use n2n\bind\err\BindMismatchException;
use n2n\bind\err\UnresolvableBindableException;
use n2n\bind\build\impl\Bind;
use n2n\bind\mapper\impl\Mappers;
use n2n\validation\plan\ErrorMap;
use valobj\impl\string\mock\SubName;

class NameTest extends TestCase {


	function testConstruct(): void {
		$name = ValueObjects::name('Testerich');
		$this->assertTrue($name instanceof Name);
		$this->assertEquals('Testerich', $name->toScalar());

		//as long only visible chars are used (and maybe spaces between) almost anything is possible even emojis
		$name = ValueObjects::name('🔧N2N-Works🔧');
		$this->assertEquals('🔧N2N-Works🔧', $name->toScalar());
	}

	function testConstructExceptionBecauseToLong(): void {
		$this->expectException(IllegalStateException::class);
		//an arabic name that is way too long, because it includes father, grandfather and epithet
		ValueObjects::name('Hadschi Rafiq Tariq Ben Hadschi Nabil Kamal Ibn Hadschi Faris al Farouk');
	}

	function testNameValueObjectExpectExceptionBecauseNotClean() {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('name',
				ValueObjects::name(' ​äüö‍‍‍àéè+‌"*ç%‎‏&/'));
	}

	function testNameValueObjectExpectExceptionBecauseLeadingSpaceIsNotClean() {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('name', ValueObjects::name(' asdf'));
	}

	function testNameValueObjectExpectExceptionBecauseTrailingSpaceIsNotClean() {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('name', ValueObjects::name('asdf '));
	}

	/**
	 * @throws BindTargetException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshal(): void {
		$result = Bind::values('Testerich', null)
				->map(Mappers::unmarshal(Name::class))
				->toValue()
				->exec();

		$this->assertEquals(new Name('Testerich'), $result->get()[0]);
		$this->assertNull($result->get()[1]);
	}

	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testUnmarshalValFail(): void {
		$result = Bind::values('Hadschi Rafiq Tariq Ben Hadschi Nabil Kamal Ibn Hadschi Faris al Farouk')
				->map(Mappers::unmarshal(Name::class))
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
		$result = Bind::values(new Name('Testerich'), null)
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
		$result = Bind::values('myname')
				->map(Mappers::unmarshal(SubName::class))
				->toValue()
				->exec();

		$subName = $result->get();
		$this->assertInstanceOf(SubName::class, $subName);
		$this->assertEquals(new SubName('myname'), $subName);
	}
}
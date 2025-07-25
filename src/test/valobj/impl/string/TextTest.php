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
use valobj\string\Text;
use n2n\spec\valobj\err\IllegalValueException;

class TextTest extends TestCase {


	function testConstruct(): void {
		$text = ValueObjects::text('Testerich');
		$this->assertEquals('Testerich', $text->toScalar());

		//as long only visible chars are used (and maybe spaces between) almost anything is possible even emojis
		$text = ValueObjects::text('🔧N2N-Works🔧');
		$this->assertEquals('🔧N2N-Works🔧', $text->toScalar());

		$text = ValueObjects::text(str_repeat('s', 5000));
		$this->assertEquals(str_repeat('s', 5000), $text->toScalar());

		$this->assertEquals(' asdf', ValueObjects::text(' asdf'));
		$this->assertEquals('asdf ', ValueObjects::text('asdf '));
	}

	function testConstructExceptionBecauseToLong(): void {
		$this->expectException(IllegalStateException::class);
		ValueObjects::text(str_repeat('s', 5001));
	}

	function testTextValueObjectExpectExceptionBecauseNotClean() {
		$this->expectException(IllegalStateException::class);
		$this->assertEquals('name',
				ValueObjects::text(' ​äüö‍‍‍àéè+‌"*ç%‎‏&/'));
	}

	/**
	 * @throws BindTargetException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 * @throws IllegalValueException
	 */
	function testUnmarshal(): void {
		$result = Bind::values('Testerich', 'Testerich' . PHP_EOL . 'von Testen', null)
				->map(Mappers::unmarshal(Text::class))
				->toValue()
				->exec();

		$this->assertEquals(new Text('Testerich'), $result->get()[0]);
		$this->assertEquals(new Text('Testerich' . PHP_EOL . 'von Testen'), $result->get()[1]);
		$this->assertNull($result->get()[2]);
	}

	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testUnmarshalValFail(): void {
		$result = Bind::values(str_repeat('s', 5001))
				->map(Mappers::unmarshal(Text::class))
				->toValue()
				->exec();

		$this->assertFalse($result->isValid());
		$errorMap = $result->getErrorMap();
		$this->assertTrue(assert($errorMap instanceof ErrorMap));
		$this->assertEquals('Maxlength [maxlength = 5000]', (string) $errorMap->getAllMessages()[0]);
	}


	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testMarshal(): void {
		$result = Bind::values(new Text('Testerich'), null)
				->map(Mappers::marshal())
				->toValue()
				->exec();

		$this->assertEquals('Testerich', $result->get()[0]);
		$this->assertNull($result->get()[1]);
	}
}
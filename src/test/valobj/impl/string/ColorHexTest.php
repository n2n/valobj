<?php

namespace valobj\impl\string;

use PHPUnit\Framework\TestCase;
use n2n\spec\valobj\err\IllegalValueException;
use valobj\ValueObjects;
use n2n\bind\build\impl\Bind;
use n2n\bind\mapper\impl\Mappers;
use n2n\bind\err\BindTargetException;
use n2n\bind\err\BindMismatchException;
use n2n\bind\err\UnresolvableBindableException;
use n2n\validation\plan\ErrorMap;
use valobj\string\colorHex;

class ColorHexTest extends TestCase {

	function setUp(): void {

	}

	function testConstruct(): void {
		$colorHex = ValueObjects::colorHex('#00ffaa');

		$this->assertEquals('#00ffaa', $colorHex->toScalar());
		$this->assertEquals('#00ffaa', (string) $colorHex);
	}

	function testNoFence(): void {
		$this->expectException(IllegalValueException::class);
		new colorHex('00ffaa');
	}

	function testUppercase(): void {
		$this->expectException(IllegalValueException::class);
		new colorHex('#00FFAA');
	}

	/**
	 * @throws BindTargetException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshal(): void {
		$result = Bind::values('#00FFaa', null)
				->map(Mappers::unmarshal(ColorHex::class))
				->toValue()
				->exec();

		$this->assertEquals(new ColorHex('#00ffaa'), $result->get()[0]);
		$this->assertNull($result->get()[1]);
	}

	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testUnmarshalValFail(): void {
		$result = Bind::values('00ffaa')
				->map(Mappers::unmarshal(ColorHex::class))
				->toValue()
				->exec();

		$this->assertFalse($result->isValid());
		$errorMap = $result->getErrorMap();
		$this->assertTrue(assert($errorMap instanceof ErrorMap));
		$this->assertEquals('Hex Color', (string) $errorMap->getAllMessages()[0]);
	}


	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testMarshal(): void {
		$result = Bind::values(new ColorHex('#00ffaa'), null)
				->map(Mappers::marshal())
				->toValue()
				->exec();

		$this->assertEquals('#00ffaa', $result->get()[0]);
		$this->assertNull($result->get()[1]);
	}
}
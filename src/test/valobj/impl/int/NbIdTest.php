<?php

namespace valobj\impl\int;

use valobj\ValueObjects;
use n2n\util\ex\IllegalStateException;
use valobj\int\NbId;
use PHPUnit\Framework\TestCase;
use n2n\bind\err\BindTargetException;
use n2n\bind\err\BindMismatchException;
use n2n\bind\err\UnresolvableBindableException;
use n2n\bind\build\impl\Bind;
use n2n\bind\mapper\impl\Mappers;
use n2n\validation\plan\ErrorMap;
use valobj\impl\int\mock\SubNbId;
use n2n\spec\valobj\err\IllegalValueException;

class NbIdTest extends TestCase {

	function testConstruct(): void {
		$nbId = ValueObjects::nbId(1);
		$this->assertSame(1, $nbId->toScalar());
	}

	function testConstructExceptionBecauseToSmall(): void {
		$this->expectException(IllegalStateException::class);
		ValueObjects::nbId(0);
	}

	/**
	 * @throws BindTargetException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshal(): void {
		$result = Bind::values(1, null)
				->map(Mappers::unmarshal(NbId::class))
				->toValue()
				->exec();

		$this->assertEquals(ValueObjects::nbId(1), $result->get()[0]);
		$this->assertNull($result->get()[1]);
	}

	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testUnmarshalValFail(): void {
		$result = Bind::values(0)
				->map(Mappers::unmarshal(NbId::class))
				->toValue()
				->exec();

		$this->assertFalse($result->isValid());
		$errorMap = $result->getErrorMap();
		$this->assertTrue(assert($errorMap instanceof ErrorMap));
		$this->assertEquals('Min [min = 1]', (string) $errorMap->getAllMessages()[0]);
	}

	/**
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 * @throws IllegalValueException
	 */
	function testUnmarshalSubclass(): void {
		$result = Bind::values(1)
				->map(Mappers::unmarshal(SubNbId::class))
				->toValue()
				->exec();

		$subNbId = $result->get();
		$this->assertInstanceOf(SubNbId::class, $subNbId);
		$this->assertEquals(new SubNbId(1), $subNbId);
	}
}
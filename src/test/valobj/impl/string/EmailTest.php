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
use valobj\string\Email;

class EmailTest extends TestCase {

	function setUp(): void {

	}

	function testConstruct(): void {
		$email = ValueObjects::email('holeradio@huii.ch');

		$this->assertEquals('holeradio@huii.ch', $email->toScalar());
		$this->assertEquals('holeradio@huii.ch', (string) $email);
	}

	function testNoAt(): void {
		$this->expectException(IllegalValueException::class);
		new Email('holeradio(at)huii.ch');
	}

	function testUppercase(): void {
		$this->expectException(IllegalValueException::class);
		new Email('hoLeradio@huii.ch');
	}

	/**
	 * @throws BindTargetException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshal(): void {
		$result = Bind::values('holeradio@huii.ch', null)
				->map(Mappers::unmarshal(Email::class))
				->toValue($v)
				->exec();

		$this->assertEquals(new Email('holeradio@huii.ch'), $result->get()[0]);
		$this->assertNull($result->get()[1]);
	}

	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testUnmarshalValFail(): void {
		$result = Bind::values('holeradiohuii.ch')
				->map(Mappers::unmarshal(Email::class))
				->toValue()
				->exec();

		$this->assertFalse($result->isValid());
		$errorMap = $result->getErrorMap();
		$this->assertTrue(assert($errorMap instanceof ErrorMap));
		$this->assertEquals('Email', (string) $errorMap->getAllMessages()[0]);
	}


	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testMarshal(): void {
		$result = Bind::values(new Email('holeradio@huii.ch'), null)
				->map(Mappers::marshal())
				->toValue()
				->exec();

		$this->assertEquals('holeradio@huii.ch', $result->get()[0]);
		$this->assertNull($result->get()[1]);
	}
}
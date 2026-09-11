<?php

namespace valobj\impl\string;

use PHPUnit\Framework\TestCase;
use n2n\bind\err\BindTargetException;
use n2n\bind\err\BindMismatchException;
use n2n\bind\err\UnresolvableBindableException;
use n2n\bind\build\impl\Bind;
use n2n\bind\mapper\impl\Mappers;
use n2n\validation\plan\ErrorMap;
use valobj\impl\string\mock\SubPasswordHash;
use valobj\string\crypt\PasswordHash;
use n2n\spec\valobj\err\IllegalValueException;

class PasswordHashTest extends TestCase {


	function testConstruct(): void {
		$rawPassword = 'Testerich';
		$passwordHash = PasswordHash::fromPassword($rawPassword);
		$this->assertTrue($passwordHash->matchesPassword($rawPassword));

		//as long only visible chars are used (and maybe spaces between) almost anything is possible even emojis
		$rawPassword = '🔧N2N-Works🔧';
		$passwordHash = PasswordHash::fromPassword($rawPassword);
		$this->assertTrue($passwordHash->matchesPassword($rawPassword));

		$rawPassword = ' ​äüö‍‍‍àéè+‌"*ç%‎‏&/';
		$passwordHash = PasswordHash::fromPassword($rawPassword);
		$this->assertTrue($passwordHash->matchesPassword($rawPassword));
	}

	/**
	 * @throws IllegalValueException
	 */
	function testHashPassword(): void {
		$rawPassword = 'Testerich';
		$passwordHash = PasswordHash::fromPassword($rawPassword);
		$passwordHash2 = new PasswordHash($passwordHash);
		$this->assertTrue($passwordHash->matchesPassword($rawPassword));
		$this->assertTrue($passwordHash2->matchesPassword($rawPassword));
	}

	/**
	 * @throws BindTargetException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshal(): void {
		$result = Bind::values('Testerich', null)
				->map(Mappers::unmarshal(PasswordHash::class))
				->toValue()
				->exec();

		$passwordHash = $result->get()[0];
		$this->assertTrue($passwordHash->matchesPassword('Testerich'));
		$this->assertNull($result->get()[1]);
	}

	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testUnmarshalValFailMinLength(): void {
		$result = Bind::values('')
				->map(Mappers::unmarshal(PasswordHash::class))
				->toValue()
				->exec();

		$this->assertFalse($result->isValid());
		$errorMap = $result->getErrorMap();
		$this->assertTrue(assert($errorMap instanceof ErrorMap));
		$this->assertEquals('Minlength [minlength = 1]', (string) $errorMap->getAllMessages()[0]);
	}

	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testMarshal(): void {
		$result = Bind::values(new PasswordHash('Testerich'), null)
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
				->map(Mappers::unmarshal(SubPasswordHash::class))
				->toValue()
				->exec();

		$hashedPassword = $result->get();
		$this->assertInstanceOf(SubPasswordHash::class, $hashedPassword);
		$this->assertTrue($hashedPassword->matchesPassword('very-short'));

	}
}
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
		$passwordHash = PasswordHash::from($rawPassword);
		$this->assertTrue(PasswordHash::verifyPassword($rawPassword, $passwordHash));

		//as long only visible chars are used (and maybe spaces between) almost anything is possible even emojis
		$rawPassword = '🔧N2N-Works🔧';
		$passwordHash = PasswordHash::from($rawPassword);
		$this->assertTrue(PasswordHash::verifyPassword($rawPassword, $passwordHash));

		$rawPassword = ' ​äüö‍‍‍àéè+‌"*ç%‎‏&/';
		$passwordHash = PasswordHash::from($rawPassword);
		$this->assertTrue(PasswordHash::verifyPassword($rawPassword, $passwordHash));
	}

	/**
	 * @throws IllegalValueException
	 */
	function testHashPassword(): void {
		$rawPassword = 'Testerich';
		$passwordHash = PasswordHash::from($rawPassword);
		$passwordHash2 = new PasswordHash($passwordHash, false);
		$passwordHash3 = new PasswordHash($rawPassword, true);
		$this->assertTrue(PasswordHash::verifyPassword($rawPassword, $passwordHash));
		$this->assertTrue(PasswordHash::verifyPassword($rawPassword, $passwordHash2));
		$this->assertTrue(PasswordHash::verifyPassword($rawPassword, $passwordHash3));
	}

	/**
	 * @throws IllegalValueException
	 */
	function testConstructExceptionBecauseToLong(): void {
		$this->expectException(IllegalValueException::class);
		$this->expectExceptionMessage('Value too long:');
		new PasswordHash(str_repeat('s', 64), true);
	}

	/**
	 * @throws IllegalValueException
	 */
	function testConstructLengthOnlyApplyForRaw(): void {
		$this->expectException(IllegalValueException::class);
		$this->expectExceptionMessage('Hash is too long:');
		new PasswordHash(str_repeat('s', 256), false);
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

		$this->assertTrue(PasswordHash::verifyPassword('Testerich', $result->get()[0]));
		$this->assertNull($result->get()[1]);
	}

	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testUnmarshalValFailMaxLength(): void {
		$result = Bind::values(str_repeat('s', 256))
				->map(Mappers::unmarshal(PasswordHash::class))
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
		$this->assertTrue(PasswordHash::verifyPassword('very-short', $hashedPassword));

	}
}
<?php

namespace valobj\impl\string\crypt;

use n2n\bind\build\impl\Bind;
use n2n\bind\err\BindMismatchException;
use n2n\bind\err\BindTargetException;
use n2n\bind\err\UnresolvableBindableException;
use n2n\bind\mapper\impl\Mappers;
use n2n\spec\valobj\err\IllegalValueException;
use n2n\util\crypt\PlainSecret;
use n2n\util\ex\err\ConfigurationError;
use PHPUnit\Framework\TestCase;
use valobj\string\crypt\EnvEncryptedSecret;

class EnvEncryptedSecretTest extends TestCase {
	private const TEST_KEY = '0123456789abcdef0123456789abcdef';

	private string|false $previousKey;

	protected function setUp(): void {
		$this->previousKey = getenv(EnvEncryptedSecret::KEY_ENVIRONMENT_VARIABLE_NAME);
		putenv(EnvEncryptedSecret::KEY_ENVIRONMENT_VARIABLE_NAME . '=' . self::TEST_KEY);
	}

	protected function tearDown(): void {
		if ($this->previousKey === false) {
			return;
		}

		putenv(EnvEncryptedSecret::KEY_ENVIRONMENT_VARIABLE_NAME . '=' . $this->previousKey);
	}

	/**
	 * @throws IllegalValueException
	 */
	function testEncrypt(): void {
		$encryptedSecret = EnvEncryptedSecret::encrypt(PlainSecret::fromString('secret-api-key'));

		$this->assertNotSame('secret-api-key', $encryptedSecret->toScalar());
		$this->assertSame('secret-api-key', $encryptedSecret->toPlainSecret()->reveal());
		$this->assertSame('secret-api-key', (new EnvEncryptedSecret($encryptedSecret->toScalar()))->toPlainSecret()->reveal());
	}

	/**
	 * @throws BindTargetException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshal(): void {
		$result = Bind::values('secret-api-key', null)
				->map(Mappers::unmarshal(EnvEncryptedSecret::class))
				->toValue()
				->exec();

		$this->assertInstanceOf(EnvEncryptedSecret::class, $result->get()[0]);
		$this->assertSame('secret-api-key', $result->get()[0]->toPlainSecret()->reveal());
		$this->assertNull($result->get()[1]);
	}

	function testConstructInvalidEncryptedSecret(): void {
		$this->expectException(IllegalValueException::class);

		new EnvEncryptedSecret('not-encrypted');
	}

	/**
	 * @throws IllegalValueException
	 */
	function testMissingEnvironmentVariable(): void {
		putenv(EnvEncryptedSecret::KEY_ENVIRONMENT_VARIABLE_NAME);

		$this->expectException(ConfigurationError::class);
		EnvEncryptedSecret::encrypt(PlainSecret::fromString('secret-api-key'));
	}

	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException|IllegalValueException
	 */
	function testMarshalNotSupported(): void {
		$encryptedSecret = EnvEncryptedSecret::encrypt(PlainSecret::fromString('secret-api-key'));

		$this->expectException(BindMismatchException::class);
		Bind::values($encryptedSecret)
				->map(Mappers::marshal())
				->toValue()
				->exec();
	}
}

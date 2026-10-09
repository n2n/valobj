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
use n2n\util\ex\IllegalStateException;
use PHPUnit\Framework\TestCase;
use valobj\string\crypt\EnvEncryptedSecret;
use valobj\impl\string\mock\SubEnvEncryptedSecret;

class EnvEncryptedSecretTest extends TestCase {
	private const TEST_KEY = '0123456789abcdef0123456789abcdef';
	private const SUB_TEST_KEY = '01fd456789abcdef0123456789abcdef';

	protected function setUp(): void {
		putenv(EnvEncryptedSecret::KEY_ENVIRONMENT_VARIABLE_NAME . '=' . self::TEST_KEY);
	}

	/**
	 * @throws IllegalValueException
	 */
	function testEncrypt(): void {
		$encryptedSecret = EnvEncryptedSecret::fromUnencrypted(PlainSecret::fromString('secret-api-key'));

		$this->assertNotSame('secret-api-key', $encryptedSecret->toScalar());
		$this->assertSame('secret-api-key', $encryptedSecret->toPlainSecret()->reveal());
		$this->assertSame('secret-api-key',
				(new EnvEncryptedSecret($encryptedSecret->toScalar()))->toPlainSecret()->reveal());
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
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 */
	function testMarshalNotSupported(): void {
		$encryptedSecret = EnvEncryptedSecret::fromUnencrypted(PlainSecret::fromString('secret-api-key'));

		$this->expectException(BindMismatchException::class);
		Bind::values($encryptedSecret)
				->map(Mappers::marshal())
				->toValue()
				->exec();
	}

	/**
	 * @throws IllegalValueException
	 */
	function testMissingSubEnvironmentVariable(): void {
		putenv(SubEnvEncryptedSecret::KEY_ENVIRONMENT_VARIABLE_NAME);

		$this->expectException(ConfigurationError::class);
		SubEnvEncryptedSecret::checkedFrom(PlainSecret::fromString('secret-api-key'));
	}

	/**
	 * @throws BindTargetException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshalSub(): void {
		putenv(SubEnvEncryptedSecret::KEY_ENVIRONMENT_VARIABLE_NAME . '=' . self::TEST_KEY);

		$result = Bind::values('secret-api-key', null)
				->map(Mappers::unmarshal(SubEnvEncryptedSecret::class))
				->toValue()
				->exec();

		$this->assertInstanceOf(SubEnvEncryptedSecret::class, $result->get()[0]);
		$this->assertSame('secret-api-key', $result->get()[0]->toPlainSecret()->reveal());
		$this->assertNull($result->get()[1]);
	}

	/**
	 * @throws IllegalValueException
	 */
	function testFromUnencrypted(): void {
		$encryptedStringSecret = EnvEncryptedSecret::fromUnencrypted('secret-api-key');
		$encryptedPlainSecret = EnvEncryptedSecret::fromUnencrypted(new PlainSecret('plain-secret'));

		$this->assertSame('secret-api-key', $encryptedStringSecret->toPlainSecret()->reveal());
		$this->assertSame('plain-secret', $encryptedPlainSecret->toPlainSecret()->reveal());
		$this->assertNull(EnvEncryptedSecret::fromUnencrypted(null));
	}

	/**
	 * @throws IllegalValueException
	 */
	function testFromUnencryptedRoundTripAndNull(): void {
		$encryptedSecret = EnvEncryptedSecret::fromUnencrypted('checked-secret');

		$this->assertSame('checked-secret', $encryptedSecret->toPlainSecret()->reveal());

		$this->assertSame('checked-secret', (new EnvEncryptedSecret($encryptedSecret->toScalar()))
				->toPlainSecret()->reveal());

		$this->assertNull(EnvEncryptedSecret::fromUnencrypted(null));
	}

	function testFromUnencryptedMissingEnvironmentVariable(): void {
		putenv(EnvEncryptedSecret::KEY_ENVIRONMENT_VARIABLE_NAME);

		$this->expectException(ConfigurationError::class);
		EnvEncryptedSecret::fromUnencrypted('secret');
	}

	/**
	 * @throws IllegalValueException
	 */
	function testMissingEnvironmentVariable(): void {
		putenv(EnvEncryptedSecret::KEY_ENVIRONMENT_VARIABLE_NAME);

		$this->expectException(ConfigurationError::class);
		EnvEncryptedSecret::checkedFrom(PlainSecret::fromString('secret-api-key'));
	}

	/**
	 * @throws IllegalValueException
	 */
	function testCheckedUnencryptedFactoryPreservesSubclassAndNull(): void {
		putenv(SubEnvEncryptedSecret::KEY_ENVIRONMENT_VARIABLE_NAME . '=' . self::SUB_TEST_KEY);
		$secret = SubEnvEncryptedSecret::checkedFromUnencrypted('subclass-secret');
		$this->assertInstanceOf(SubEnvEncryptedSecret::class, $secret);
		$this->assertSame('subclass-secret', $secret->toPlainSecret()->reveal());
		$this->assertSame($secret->toScalar(), SubEnvEncryptedSecret::checkedFrom($secret->toScalar())->toScalar());
		$this->assertNull(SubEnvEncryptedSecret::checkedFromUnencrypted(null));
	}

	function testCheckedFactoryRejectsMalformedEnvelope(): void {
		$values = [
			'null', '"string"', 'not-json', '{}',
		];


		foreach ($values as $value) {
			try {
				EnvEncryptedSecret::checkedFrom($value);
				$this->fail('Malformed secret was accepted: ' . $value);
			} catch (IllegalValueException $e) {
				$this->assertStringContainsString('Invalid encrypted secret', $e->getMessage());
			}
		}
	}

	function testInvalidNonceEncodingPropagatesFromDecryptor(): void {
		$this->expectException(\InvalidArgumentException::class);
		EnvEncryptedSecret::checkedFrom('{"nonce":"%%%","tag":"aA==","ciphertext":"aA=="}');
	}

	function testInvalidTagEncodingPropagatesFromDecryptor(): void {
		$this->expectException(\InvalidArgumentException::class);
		EnvEncryptedSecret::checkedFrom('{"nonce":"aA==","tag":"%%%","ciphertext":"aA=="}');
	}

	function testInvalidCiphertextEncodingPropagatesFromDecryptor(): void {
		$this->expectException(\InvalidArgumentException::class);
		EnvEncryptedSecret::checkedFrom('{"nonce":"aA==","tag":"aA==","ciphertext":"%%%"}');
	}

	/**
	 * @throws IllegalValueException
	 */
	function testCheckedFactoryPropagatesMissingConfiguration(): void {
		putenv(EnvEncryptedSecret::KEY_ENVIRONMENT_VARIABLE_NAME);
		$this->expectException(ConfigurationError::class);
		EnvEncryptedSecret::checkedFromUnencrypted('secret');
	}

}

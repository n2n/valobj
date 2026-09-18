<?php

namespace valobj\string\crypt;

use n2n\bind\attribute\impl\Unmarshal;
use n2n\bind\mapper\Mapper;
use n2n\bind\mapper\impl\Mappers;
use n2n\spec\valobj\err\IllegalValueException;
use n2n\util\crypt\ex\DecryptionFailedException;
use n2n\util\crypt\ex\EncryptionFailedException;
use n2n\util\crypt\PlainSecret;
use n2n\util\crypt\symmetric\EncryptedSecret;
use n2n\util\crypt\symmetric\SymmetricCryptUtils;
use n2n\util\ex\err\ConfigurationError;
use n2n\util\type\attrs\AttributesException;
use n2n\util\type\TypeConstraints;
use n2n\validation\validator\impl\Validators;
use valobj\string\StringValueObjectAdapter;

class EnvEncryptedSecret extends StringValueObjectAdapter {

	const KEY_ENVIRONMENT_VARIABLE_NAME = 'SECRET_ENCRYPTION_KEY';

	/**
	 * encrypted representation
	 */
	private string $encryptedValue;

	final function __construct(string $value) {
		parent::__construct($value);

		// Validate encrypted representation and configured key.
		static::decrypt($value);

		$this->encryptedValue = $value;
	}

	/**
	 * @throws IllegalValueException
	 */
	static function fromUnencrypted(PlainSecret|string|null $plainSecret): static|null {
		if ($plainSecret === null) {
			return null;
		}

		$plainSecret = is_string($plainSecret) ? PlainSecret::fromString($plainSecret) : $plainSecret;
		$key = static::readKey();

		try {
			return new static(SymmetricCryptUtils::encrypt($plainSecret, $key)->toJson());
		} catch (EncryptionFailedException $e) {
			throw new IllegalValueException('Could not encrypt secret.', previous: $e);
		}
	}

	#[Unmarshal]
	static function unmarshalMapper(): Mapper {
		return static::encryptMapper();
	}

	static function encryptMapper(): Mapper {
		return Mappers::pipe(Mappers::type(TypeConstraints::string(true)),
				Validators::minlength(1),
				Mappers::valueIfNotNull(fn(string $value) => static::fromUnencrypted($value))
		);
	}

	/**
	 * @throws IllegalValueException
	 */
	function toPlainSecret(): PlainSecret {
		return static::decrypt($this->encryptedValue);
	}

	/**
	 * @throws IllegalValueException
	 */
	private static function decrypt(string $value): PlainSecret {
		$key = static::readKey();

		try {
			$encryptedSecret = EncryptedSecret::fromJson($value);
			return SymmetricCryptUtils::decrypt($encryptedSecret, $key);
		} catch (AttributesException|DecryptionFailedException|\InvalidArgumentException $e) {
			throw new IllegalValueException('Invalid encrypted secret.', previous: $e);
		}
	}

	private static function readKey(): string {
		$key = getenv(static::KEY_ENVIRONMENT_VARIABLE_NAME);
		if (!is_string($key)) {
			throw new ConfigurationError('Env var for ' . static::class . ' not set: '
					. static::KEY_ENVIRONMENT_VARIABLE_NAME, file: '');
		}

		return $key;
	}
}
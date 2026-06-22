<?php
namespace valobj\string\crypt;

use n2n\bind\attribute\impl\Unmarshal;
use n2n\bind\mapper\Mapper;
use n2n\bind\mapper\impl\Mappers;
use n2n\spec\valobj\err\IllegalValueException;
use n2n\util\crypt\PlainSecret;
use n2n\util\crypt\symmetric\EncryptedSecret;
use n2n\util\crypt\symmetric\SymmetricCryptUtils;
use n2n\util\type\TypeConstraints;
use n2n\util\type\attrs\AttributesException;
use valobj\string\StringValueObjectAdapter;
use n2n\util\crypt\ex\DecryptionFailedException;
use n2n\util\crypt\ex\EncryptionFailedException;
use n2n\util\ex\err\ConfigurationError;
use n2n\validation\validator\impl\Validators;

class EnvEncryptedSecret extends StringValueObjectAdapter {

	const KEY_ENVIRONMENT_VARIABLE_NAME = 'SECRET_ENCRYPTION_KEY';

	private PlainSecret $plainSecret;

	function __construct(string $value) {
		parent::__construct($value);

		try {
			$this->plainSecret = SymmetricCryptUtils::decrypt(EncryptedSecret::fromJson($value), static::readKey());
		} catch (AttributesException|DecryptionFailedException|\InvalidArgumentException $e) {
			throw new IllegalValueException('Invalid encrypted secret.', previous: $e);
		}
	}

	/**
	 * @throws IllegalValueException
	 */
	static function encrypt(PlainSecret $plainSecret): static {
		try {
			return new static(SymmetricCryptUtils::encrypt($plainSecret, static::readKey())->toJson());
		} catch (EncryptionFailedException $e) {
			throw new IllegalValueException('Could not encrypt secret.', previous: $e);
		}
	}

	#[Unmarshal]
	static function unmarshalMapper(): Mapper {
		return static::encryptMapper();
	}

	static function encryptMapper(): Mapper {
		return Mappers::pipe(
				Mappers::type(TypeConstraints::string(true)),
				Validators::minlength(1),
				Mappers::valueIfNotNull(fn(string $value) => static::encrypt(PlainSecret::fromString($value))));
	}

	function toPlainSecret(): PlainSecret {
		return $this->plainSecret;
	}

	/**
	 * @return string
	 */
	private static function readKey(): string {
		$key = getenv(static::KEY_ENVIRONMENT_VARIABLE_NAME);
		if (!is_string($key)) {
			throw new ConfigurationError('env var not set: ' . static::KEY_ENVIRONMENT_VARIABLE_NAME, __FILE__, __LINE__);
		}

		return $key;
	}
}

<?php

namespace valobj\string\crypt;

use n2n\bind\attribute\impl\Unmarshal;
use n2n\bind\mapper\Mapper;
use n2n\bind\attribute\impl\Marshal;
use n2n\bind\mapper\impl\Mappers;
use n2n\util\ex\ExUtils;
use n2n\util\HashUtils;
use n2n\validation\validator\impl\Validators;
use valobj\string\StringValueObjectAdapter;
use n2n\util\ex\err\ConfigurationError;
use n2n\spec\valobj\err\IllegalValueException;
use n2n\validation\validator\impl\ValidationUtils;

class PasswordHash extends StringValueObjectAdapter {
	const MIN_LENGTH = 1;
	const MAX_LENGTH = 63;
	const MAX_HASH_LENGTH = 255;

	public final function __construct(string $value, $raw = false) {
		parent::__construct($value);

		if (static::MIN_LENGTH < 1) {
			throw new ConfigurationError('Illegal MIN_LENGTH constant defined in ' . static::class
					. '. Value must be at least 1.');
		}

		if ($raw === true) {
			IllegalValueException::assertTrue(ValidationUtils::maxlength($this->value, static::MAX_LENGTH),
					'Raw Password Value too long: ' . $this->value . ' max length = ' . static::MAX_LENGTH);
			IllegalValueException::assertTrue(ValidationUtils::minlength($this->value, static::MIN_LENGTH),
					'Raw Password Value too short: ' . $this->value . ' min length = ' . static::MIN_LENGTH);
			$this->value = HashUtils::hashPassword($value);
		}

		IllegalValueException::assertTrue(ValidationUtils::maxlength($this->value, static::MAX_HASH_LENGTH),
				'Hash is too long: ' . $this->value);
	}


	#[Unmarshal]
	static function unmarshalMapper(): Mapper {
		$class = new \ReflectionClass(static::class);
		return Mappers::pipe(
				Validators::minlength(minlength: static::MIN_LENGTH),
				Validators::maxlength(maxlength: static::MAX_LENGTH),
				Mappers::valueIfNotNull(fn(string $value) => $class->newInstance(HashUtils::hashPassword($value))));
	}

	#[Marshal]
	static function marshalMapper(): Mapper {
		return Mappers::value(fn(PasswordHash $passwordHash) => $passwordHash->toScalar());
	}

	static function from(string|\Stringable|null $value, bool $lenient = false): ?static {
		return ExUtils::try(fn () => self::checkedFrom($value, $lenient));
	}

	static function checkedFrom(string|\Stringable|null $value, bool $lenient = false): null|static {
		if ($value === null) {
			return null;
		}
		return parent::checkedFrom(HashUtils::hashPassword((string) $value), $lenient);
	}

	public static function verifyPassword(string $rawPassword, ?string $hashedPassword): bool {
		return  $hashedPassword !== null && HashUtils::verifyPassword($rawPassword, $hashedPassword);
	}
}
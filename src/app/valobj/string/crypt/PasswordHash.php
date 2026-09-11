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

class PasswordHash extends StringValueObjectAdapter {

	public final function __construct(string $value) {
		parent::__construct($value);

		if ($this->matchesPassword('')) {
			throw new IllegalValueException('Hash must not be of empty string.');
		}
	}


	#[Unmarshal]
	static function unmarshalMapper(): Mapper {
		$class = new \ReflectionClass(static::class);
		return Mappers::pipe(
				Validators::minlength(minlength: 1),
				Mappers::valueIfNotNull(fn(string $value) => $class->newInstance(HashUtils::hashPassword($value))));
	}

	#[Marshal]
	static function marshalMapper(): Mapper {
		return Mappers::value(fn(PasswordHash $passwordHash) => $passwordHash->toScalar());
	}

	static function fromPassword(string|\Stringable|null $value, bool $lenient = false): ?static {
		return ExUtils::try(fn () => self::checkedFromPassword($value, $lenient));
	}

	/**
	 * @throws IllegalValueException
	 */
	static function checkedFromPassword(string|\Stringable|null $value, bool $lenient = false): null|static {
		if ($value === null) {
			return null;
		}
		return parent::checkedFrom(HashUtils::hashPassword((string) $value), $lenient);
	}


	function matchesPassword(string $password): bool {
		return  HashUtils::verifyPassword($password, $this->value);
	}
}
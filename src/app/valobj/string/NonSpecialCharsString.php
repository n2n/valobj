<?php

namespace valobj\string;

use n2n\spec\valobj\err\IllegalValueException;
use n2n\util\io\IoUtils;
use n2n\validation\validator\impl\ValidationUtils;
use n2n\util\StringUtils;
use n2n\bind\attribute\impl\Marshal;
use n2n\bind\mapper\Mapper;
use n2n\bind\mapper\impl\Mappers;
use n2n\bind\attribute\impl\Unmarshal;
use n2n\util\ex\err\ConfigurationError;

/**
 * Usually not used by its own but as super type for example PathParts String value object.
 * any Sub can extend NonSpecialCharsString and simply override the const, and it will use the constructor and mappers below
 * with either default params, or the given in that subclass
 * so for a path Part that allow only 16 Chars, simply set const MAX_LENGTH = 16;
 */
class NonSpecialCharsString extends StringValueObjectAdapter {

	const MIN_LENGTH = 3;
	const MAX_LENGTH = 63;
	const LOWER_CASE_ONLY = true;

	/**
	 * @param string $value
	 * and between static::MIN_LENGTH and static::MAX_LENGTH chars long, static::MIN_LENGTH has to be > 0
	 * @throws IllegalValueException if passed value is invalid.
	 */
	public final function __construct(string $value) {
		parent::__construct($value);

		if (self::MIN_LENGTH < 1) {
			throw new ConfigurationError('Illegal MIN_LENGTH constant defined in ' . static::class
					. '. Value must be at least 1.');
		}

		IllegalValueException::assertTrue(ValidationUtils::maxlength($this->value, static::MAX_LENGTH),
				'Value too long: ' . $this->value);
		IllegalValueException::assertTrue(ValidationUtils::minlength($this->value, static::MIN_LENGTH),
				'Value too short: ' . $this->value);
		IllegalValueException::assertTrue(!IoUtils::hasSpecialChars($value),
				'Value contains special chars: ' . $this->value);
		if (static::LOWER_CASE_ONLY) {
			IllegalValueException::assertTrue(ValidationUtils::isLowerCaseOnly($this->value),
					'Value not lowercase: ' . $this->value);
		}
	}

	#[Marshal]
	static function marshalMapper(): Mapper {
		return Mappers::value(fn(NonSpecialCharsString $nonSpecialCharsString) => $nonSpecialCharsString->toScalar());
	}

	#[Unmarshal]
	static function unmarshalMapper(): Mapper {
		$class = new \ReflectionClass(static::class);

		return Mappers::pipe(
				Mappers::noSpecialChars(false, static::LOWER_CASE_ONLY,
						minlength: static::MIN_LENGTH, maxlength: static::MAX_LENGTH),
				Mappers::valueIfNotNull(fn(string $value) => $class->newInstance($value)));
	}
}
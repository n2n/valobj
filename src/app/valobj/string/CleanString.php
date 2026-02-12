<?php

namespace valobj\string;

use n2n\spec\valobj\err\IllegalValueException;
use n2n\validation\validator\impl\ValidationUtils;
use n2n\util\StringUtils;
use n2n\bind\attribute\impl\Marshal;
use n2n\bind\mapper\Mapper;
use n2n\bind\mapper\impl\Mappers;
use n2n\bind\attribute\impl\Unmarshal;

/**
 * Usually not used by its own but as super type of ever other String value object.
 * any Sub can extend CleanString and simply override the const, and it will use the constructor and mappers below
 * with either default params, or the given in that subclass
 * so for a label that allow only 16 Chars, simply set const MAX_LENGTH = 16;
 */
class CleanString extends StringValueObjectAdapter {

	const MIN_LENGTH = 1;
	const MAX_LENGTH = 255;
	const SIMPLE_WHITESPACES_ONLY = true;
	/**
	 * @param string $value that is clean according to {@link StringUtils::isClean} with param static::SIMPLE_WHITESPACES_ONLY
	 * and between static::MIN_LENGTH and static::MAX_LENGTH chars long, static::MIN_LENGTH has to be > 0
	 * @throws IllegalValueException if passed value is invalid.
	 */
	public final function __construct(string $value) {
		parent::__construct($value);

		IllegalValueException::assertTrue(ValidationUtils::maxlength($this->value, static::MAX_LENGTH),
				'Value too long: ' . $this->value);
		IllegalValueException::assertTrue(ValidationUtils::minlength($this->value, static::MIN_LENGTH),
				'Value too short: ' . $this->value);
		IllegalValueException::assertTrue(StringUtils::isClean($value, static::SIMPLE_WHITESPACES_ONLY),
				'Value not clean: ' . $this->value);
	}

	#[Marshal]
	static function marshalMapper(): Mapper {
		return Mappers::value(fn(CleanString $label) => $label->toScalar());
	}

	#[Unmarshal]
	static function unmarshalMapper(): Mapper {
		$class = new \ReflectionClass(static::class);
		return Mappers::pipe(
				Mappers::cleanString(minlength: static::MIN_LENGTH, maxlength: static::MAX_LENGTH, simpleWhitespacesOnly: static::SIMPLE_WHITESPACES_ONLY),
				Mappers::valueIfNotNull(fn(string $value) => $class->newInstance($value)));
	}
}
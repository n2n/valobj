<?php

namespace valobj\string;

use n2n\spec\valobj\err\IllegalValueException;
use n2n\validation\validator\impl\ValidationUtils;
use n2n\util\StringUtils;
use n2n\bind\attribute\impl\Marshal;
use n2n\bind\mapper\Mapper;
use n2n\bind\mapper\impl\Mappers;
use n2n\bind\attribute\impl\Unmarshal;

class LatinString extends StringValueObjectAdapter {
	const MIN_LENGTH = 1;
	const MAX_LENGTH = 63;

	public final function __construct(string $value) {
		parent::__construct($value);

		IllegalValueException::assertTrue(ValidationUtils::maxlength($this->value, static::MAX_LENGTH),
				'Value too long: ' . $this->value);
		IllegalValueException::assertTrue(ValidationUtils::minlength($this->value, static::MIN_LENGTH),
				'Value too short: ' . $this->value);
		IllegalValueException::assertTrue(StringUtils::isClean($value, true),
				'Value not clean: ' . $this->value);
		IllegalValueException::assertTrue(StringUtils::isLatin($value),
				'Value not UTF8, or not inside Basic Latin, Latin-1 Supplement, Latin Extended-A: ' . $this->value);
	}

	#[Marshal]
	static function marshalMapper(): Mapper {
		return Mappers::value(fn (LatinString $latinString) => $latinString->toScalar());
	}

	#[Unmarshal]
	static function unmarshalMapper(): Mapper {
		$class = new \ReflectionClass(static::class);
		return Mappers::pipe(
				Mappers::cleanString(minlength: static::MIN_LENGTH,maxlength: static::MAX_LENGTH, simpleWhitespacesOnly: true),
				Mappers::valueIfNotNull(fn (string $value) => $class->newInstance($value)));
	}
}
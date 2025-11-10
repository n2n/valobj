<?php
namespace valobj\string;

use n2n\spec\valobj\err\IllegalValueException;
use n2n\validation\validator\impl\ValidationUtils;
use n2n\util\StringUtils;
use n2n\bind\attribute\impl\Unmarshal;
use n2n\bind\attribute\impl\Marshal;
use n2n\bind\mapper\impl\Mappers;
use n2n\bind\mapper\Mapper;

class MediumLabel extends StringValueObjectAdapter {
	/**
	 * @param string $value that is clean according to {@link StringUtils::isClean} and max 127 chars long
	 * @throws IllegalValueException if passed value is invalid.
	 */
	public function __construct(string $value) {
		parent::__construct($value);
		IllegalValueException::assertTrue(ValidationUtils::maxlength($this->value, 127),
				'Value too long: ' . $this->value);
		IllegalValueException::assertTrue(StringUtils::isClean($value, true),
				'Value not clean: ' . $this->value);
	}

	#[Marshal]
	static function marshalMapper(): Mapper {
		return Mappers::valueClosure(fn (MediumLabel $name) => $name->toScalar());
	}

	#[Unmarshal]
	static function unmarshalMapper(): Mapper {
		$class = new \ReflectionClass(static::class);
		return Mappers::pipe(Mappers::cleanString(maxlength: 127),
				Mappers::valueNotNullClosure(fn (string $arg) => $class->newInstance($arg)));
	}
}
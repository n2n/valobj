<?php

namespace valobj\string;

use n2n\bind\mapper\impl\Mappers;
use n2n\bind\mapper\Mapper;
use n2n\spec\valobj\scalar\StringValueObject;
use n2n\bind\attribute\impl\Marshal;
use n2n\bind\attribute\impl\Unmarshal;
use n2n\validation\validator\impl\ValidationUtils;
use n2n\spec\valobj\err\IllegalValueException;
use n2n\validation\validator\impl\Validators;
use n2n\util\StringUtils;

class Name implements StringValueObject, \Stringable {

	public function __construct(private string $value) {
		IllegalValueException::assertTrue(ValidationUtils::maxlength($this->value, 63));
	}

	#[Marshal]
	static function marshalMapper(): Mapper {
		return Mappers::valueClosure(fn (Name $name) => $name->toScalar());
	}

	#[Unmarshal]
	static function unmarshalMapper(): Mapper {
		return Mappers::pipe(Mappers::cleanString(maxlength: 63),
				Mappers::valueNotNullClosure(fn (string $value) => new self($value)));
	}

	public function __toString(): string {
		return $this->toScalar();
	}

	function toScalar(): string {
		return $this->value;
	}
}
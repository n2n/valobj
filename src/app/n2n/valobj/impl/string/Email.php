<?php

namespace n2n\valobj\impl\string;

use n2n\util\valobj\StringValueObject;
use n2n\validation\validator\impl\ValidationUtils;
use n2n\util\valobj\IncompatibleValueException;
use n2n\valobj\attribute\BindProfile;
use n2n\bind\mapper\impl\Mappers;
use n2n\bind\mapper\Mapper;

class Email implements StringValueObject {


	public function __construct(private string $value) {
		IncompatibleValueException::assertTrue(
				ValidationUtils::isLowerCaseOnly($this->value) && ValidationUtils::isEmail($this->value));
	}

	function toValue(): string {
		return $this->value;
	}

	#[BindProfile]
	static function defaultMapper(bool $mandatory = false): Mapper {
		return Mappers::pipe(Mappers::email($mandatory), Mappers::valueNotNullClosure(fn (string $value) => new self($value)));
	}

}
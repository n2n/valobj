<?php

namespace valobj\string;

use n2n\bind\mapper\impl\Mappers;
use n2n\bind\mapper\Mapper;
use n2n\bind\attribute\impl\Marshal;
use n2n\bind\attribute\impl\Unmarshal;
use n2n\validation\validator\impl\ValidationUtils;
use n2n\spec\valobj\err\IllegalValueException;

class Email extends StringValueObjectAdapter {

	public function __construct(string $value) {
		parent::__construct($value);
		IllegalValueException::assertTrue(
				ValidationUtils::isLowerCaseOnly($this->value) && ValidationUtils::isEmail($this->value),
					'Illegal e-mail value: ' . $this->value);
	}

	#[Marshal]
	static function marshalMapper(): Mapper {
		return Mappers::valueClosure(fn (Email $email) => $email->toScalar());
	}

	#[Unmarshal]
	static function unmarshalMapper(): Mapper {
		return Mappers::pipe(Mappers::email(), Mappers::valueNotNullClosure(fn (string $email) => new self($email)));
	}
}
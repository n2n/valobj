<?php

namespace valobj\string;

use n2n\bind\mapper\impl\Mappers;
use n2n\bind\mapper\Mapper;
use n2n\bind\attribute\impl\Marshal;
use n2n\bind\attribute\impl\Unmarshal;
use n2n\validation\validator\impl\ValidationUtils;
use n2n\spec\valobj\err\IllegalValueException;
use n2n\bind\mapper\impl\string\PhoneMapper;

class Phone extends StringValueObjectAdapter {

	public function __construct(string $value) {
		parent::__construct($value);
		IllegalValueException::assertTrue(
				ValidationUtils::isPhone($this->value),
				'Illegal phone value: ' . $this->value);
	}

	static function from(string|\Stringable|null $value, bool $lenient = false): ?static {
		if ($value === null) {
			return null;
		}

		if (!$lenient) {
			return parent::from($value);
		}

		return parent::from(PhoneMapper::normalizeStr($value));
	}

	public function toTel(): ?string {
		return str_replace(['(0)', ' ', '-'], '', $this->value);
	}

	#[Marshal]
	static function marshalMapper(): Mapper {
		return Mappers::valueClosure(fn (Phone $phone) => $phone->toScalar());
	}

	#[Unmarshal]
	static function unmarshalMapper(): Mapper {
		return Mappers::pipe(Mappers::phone(), Mappers::valueNotNullClosure(fn (string $phone) => new self($phone)));
	}
}
<?php

namespace valobj\string;

use n2n\bind\mapper\impl\Mappers;
use n2n\bind\mapper\Mapper;
use n2n\bind\attribute\impl\Marshal;
use n2n\bind\attribute\impl\Unmarshal;
use n2n\validation\validator\impl\ValidationUtils;
use n2n\spec\valobj\err\IllegalValueException;

class ColorHex extends StringValueObjectAdapter {

	public function __construct(string $value) {
		parent::__construct($value);
		IllegalValueException::assertTrue(
				ValidationUtils::isLowerCaseOnly($this->value) && ValidationUtils::isColorHex($this->value),
				'Illegal Hex color value: ' . $this->value);
	}

	#[Marshal]
	static function marshalMapper(): Mapper {
		return Mappers::valueClosure(fn (ColorHex $colorHex) => $colorHex->toScalar());
	}

	#[Unmarshal]
	static function unmarshalMapper(): Mapper {
		return Mappers::pipe(Mappers::colorHex(), Mappers::valueNotNullClosure(fn (string $colorHex) => new self($colorHex)));
	}
}
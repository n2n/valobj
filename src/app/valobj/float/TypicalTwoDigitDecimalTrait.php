<?php

namespace valobj\float;

use n2n\spec\valobj\err\IllegalValueException;
use n2n\util\FloatUtils;
use n2n\bind\attribute\impl\Marshal;
use n2n\bind\mapper\Mapper;
use n2n\bind\mapper\impl\Mappers;
use n2n\bind\attribute\impl\Unmarshal;

trait TypicalTwoDigitDecimalTrait  {
	use FloatValueObjectAdapterTrait {
		FloatValueObjectAdapterTrait::__construct as parentConstruct;
	}

	function __construct(float $value) {
		IllegalValueException::assertTrue(2 >= FloatUtils::countDecimalPlaces($value),
				'Value contains more than 2 decimal places: ' . $value);
		IllegalValueException::assertTrue(-100000 <= $value);
		IllegalValueException::assertTrue(100000 >= $value);
		$this->parentConstruct($value);
	}

	#[Marshal]
	static function marshalMapper(): Mapper {
		return Mappers::valueClosure(fn (self $valObj) => $valObj->toScalar());
	}

	#[Unmarshal]
	static function unmarshalMapper(): Mapper {
		return Mappers::pipe(Mappers::float(step: 0.01),
				Mappers::valueNotNullClosure(fn (float $value) => new static($value)));
	}
}
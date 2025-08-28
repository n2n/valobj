<?php

namespace valobj\float;

use n2n\spec\valobj\err\IllegalValueException;
use n2n\util\FloatUtils;
use n2n\bind\attribute\impl\Marshal;
use n2n\bind\mapper\Mapper;
use n2n\bind\mapper\impl\Mappers;
use n2n\bind\attribute\impl\Unmarshal;

/**
 * Usually not used by its own but as super type of other value objects.
 */
class TypicalTwoDigitDecimal extends FloatValueObjectAdapter {

	function __construct(float $value) {
		IllegalValueException::assertTrue(2 >= FloatUtils::countDecimalPlaces($value),
				'Value contains more than 2 decimal places: ' . $value);
		IllegalValueException::assertTrue(-100000 <= $value);
		IllegalValueException::assertTrue(100000 >= $value);
		parent::__construct($value);
	}

	#[Marshal]
	static function marshalMapper(): Mapper {
		return Mappers::valueClosure(fn (self $valObj) => $valObj->toScalar());
	}

	#[Unmarshal]
	static function unmarshalMapper(): Mapper {
		$class = new \ReflectionClass(static::class);
		return Mappers::pipe(Mappers::float(step: 0.01),
				Mappers::valueNotNullClosure(fn (float $value) => $class->newInstance($value)));
	}
}
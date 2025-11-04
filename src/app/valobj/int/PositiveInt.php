<?php

namespace valobj\int;

use n2n\spec\valobj\err\IllegalValueException;
use n2n\bind\attribute\impl\Marshal;
use n2n\bind\mapper\Mapper;
use n2n\bind\mapper\impl\Mappers;
use n2n\bind\attribute\impl\Unmarshal;

class PositiveInt extends IntValueObjectAdapter {
	public function __construct(int $value) {
		parent::__construct($value);
		IllegalValueException::assertTrue($this->value > 0,
				'Value too small: ' . $this->value);
	}

	#[Marshal]
	static function marshalMapper(): Mapper {
		return Mappers::valueClosure(fn (PositiveInt $nbId) => $nbId->toScalar());
	}

	#[Unmarshal]
	static function unmarshalMapper(): Mapper {
		$class = new \ReflectionClass(static::class);
		return Mappers::pipe(Mappers::int(false, 1, PHP_INT_MAX),
				Mappers::valueNotNullClosure(fn (int $id) => $class->newInstance($id)));
	}
}
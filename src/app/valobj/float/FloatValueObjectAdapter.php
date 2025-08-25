<?php

namespace valobj\float;

use n2n\spec\valobj\scalar\FloatValueObject;

abstract class FloatValueObjectAdapter implements FloatValueObject, \Stringable, \JsonSerializable {

	function __construct(protected float $value) {
	}

	final function equals(FloatValueObject $floatValueObject): bool {
		return $this->toScalar() === $floatValueObject->toScalar();
	}

	function __toString(): string {
		return (string) $this->value;
	}

	final function jsonSerialize(): float {
		return $this->value;
	}

	final function toScalar(): float {
		return $this->value;
	}
}
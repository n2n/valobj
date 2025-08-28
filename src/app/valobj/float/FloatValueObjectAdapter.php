<?php

namespace valobj\float;

use n2n\spec\valobj\scalar\FloatValueObject;

abstract class FloatValueObjectAdapter implements FloatValueObject, \Stringable, \JsonSerializable {
	function __construct(protected float $value) {
	}

	function equals(FloatValueObject $floatValueObject): bool {
		return $this->toScalar() === $floatValueObject->toScalar();
	}

	function __toString(): string {
		return (string) $this->value;
	}

	function jsonSerialize(): float {
		return $this->value;
	}

	function toScalar(): float {
		return $this->value;
	}
}
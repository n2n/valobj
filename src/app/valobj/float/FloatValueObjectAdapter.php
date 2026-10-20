<?php

namespace valobj\float;

use n2n\spec\valobj\scalar\FloatValueObject;
use n2n\util\ex\ExUtils;

abstract class FloatValueObjectAdapter implements FloatValueObject, \Stringable, \JsonSerializable {
	function __construct(protected float $value) {
	}

	function equals(FloatValueObject|float|null $floatValueObject): bool {
		if ($floatValueObject === null) {
			return false;
		}

		if (is_float($floatValueObject)) {
			return $floatValueObject === $this->value;
		}

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

	static function from(FloatValueObject|float|null $value): ?static {
		if ($value === null) {
			return null;
		}

		if ($value instanceof FloatValueObject) {
			$value = $value->toScalar();
		}

		return ExUtils::try(fn () => new static($value));
	}
}
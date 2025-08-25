<?php

namespace valobj\int;

use n2n\spec\valobj\scalar\IntValueObject;

abstract class IntValueObjectAdapter implements IntValueObject, \Stringable, \JsonSerializable {

	function __construct(protected int $value) {
	}

	final function equals(IntValueObject $intValueObject): bool {
		return $this->toScalar() === $intValueObject->toScalar();
	}

	function __toString(): string {
		return (string) $this->value;
	}

	final function jsonSerialize(): int {
		return $this->value;
	}

	final function toScalar(): int {
		return $this->value;
	}
}
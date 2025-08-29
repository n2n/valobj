<?php

namespace valobj\string;

use n2n\spec\valobj\scalar\StringValueObject;
use n2n\spec\valobj\err\IllegalValueException;
use n2n\validation\validator\impl\ValidationUtils;

abstract class StringValueObjectAdapter implements StringValueObject, \Stringable, \JsonSerializable {

	function __construct(protected string $value) {
		IllegalValueException::assertTrue(ValidationUtils::minlength($this->value, 1),
				'Empty string not allowed.');
	}

	function equals(StringValueObject|string|null $stringValueObject): bool {
		if ($stringValueObject === null) {
			return false;
		}

		if (is_string($stringValueObject)) {
			return $stringValueObject === $this->value;
		}
		return $this->toScalar() === $stringValueObject->toScalar();
	}

	public function __toString(): string {
		return $this->toScalar();
	}

	function jsonSerialize(): string {
		return $this->toScalar();
	}

	function toScalar(): string {
		return $this->value;
	}
}
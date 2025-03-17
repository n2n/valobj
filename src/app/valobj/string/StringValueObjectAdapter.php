<?php

namespace valobj\string;

use n2n\spec\valobj\scalar\StringValueObject;
use n2n\util\ex\ExUtils;

abstract class StringValueObjectAdapter implements StringValueObject, \Stringable, \JsonSerializable {

	public function __toString(): string {
		return $this->toScalar();
	}

	function jsonSerialize(): string {
		return $this->toScalar();
	}
}
<?php

namespace valobj\string;

use valobj\string\StringValueObjectAdapter;
use n2n\spec\valobj\err\IllegalValueException;
use n2n\validation\validator\impl\ValidationUtils;
use n2n\util\StringUtils;

class CleanGzString extends StringValueObjectAdapter {
	const MIN_LENGTH = 1;
	const MAX_LENGTH = 100000;
	const SIMPLE_WHITESPACES_ONLY = false;

	public final function __construct(string $value) {
		parent::__construct($value);

		if (self::MIN_LENGTH < 1) {
			throw new ConfigurationError('Illegal MIN_LENGTH constant defined in ' . static::class
					. '. Value must be at least 1.');
		}

		IllegalValueException::assertTrue(ValidationUtils::maxlength($this->value, static::MAX_LENGTH),
				'Value too long: ' . $this->value);
		IllegalValueException::assertTrue(ValidationUtils::minlength($this->value, static::MIN_LENGTH),
				'Value too short: ' . $this->value);
		IllegalValueException::assertTrue(StringUtils::isClean($value, static::SIMPLE_WHITESPACES_ONLY),
				'Value not clean: ' . $this->value);
	}
	function uncompress(): ?string {
		return null;
	}

	function fromUncompressed(string $uncompressedString): ?static {
		return null;
	}
}
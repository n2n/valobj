<?php

namespace valobj\string;

use valobj\string\StringValueObjectAdapter;
use n2n\spec\valobj\err\IllegalValueException;
use n2n\validation\validator\impl\ValidationUtils;
use n2n\util\StringUtils;
use n2n\util\ex\ExUtils;
use n2n\util\ex\err\ConfigurationError;

class CleanGzString extends StringValueObjectAdapter {
	const MIN_LENGTH = 1;
	const MAX_LENGTH = 100000;
	const SIMPLE_WHITESPACES_ONLY = false;

	public final function __construct(string $value) {
		parent::__construct($value);

		IllegalValueException::assertTrue($this->isGzString($value),
				'Value is no GZ String: ' . $value);

		$uncompressedValue = $this->uncompress();

		if (self::MIN_LENGTH < 1) {
			throw new ConfigurationError('Illegal MIN_LENGTH constant defined in ' . static::class
					. '. Value must be at least 1.');
		}

		IllegalValueException::assertTrue(ValidationUtils::maxlength($uncompressedValue, static::MAX_LENGTH),
				'Value too long: ' . $uncompressedValue);
		IllegalValueException::assertTrue(ValidationUtils::minlength($uncompressedValue, static::MIN_LENGTH),
				'Value too short: ' . $uncompressedValue);
		IllegalValueException::assertTrue(StringUtils::isClean($uncompressedValue, static::SIMPLE_WHITESPACES_ONLY),
				'Value not clean: ' . $uncompressedValue);
	}

	function uncompress(): ?string {
		if($this->isGzString($this->value)) {
			return gzuncompress($this->value);
		}
		return null;
	}

	static function fromUncompressed(?string $uncompressedString): ?static {
		if ($uncompressedString === null) {
			return null;
		}
		return ExUtils::try(fn () => new static(gzcompress($uncompressedString, 9)));
	}

	/**
	 * determines if a string is a gzipped string supporting strings
	 * encoded with either gzencode or gzcompress
	 *
	 * @param string $string the string to check for compression
	 * @return bool whether or not the string was compmressed
	 */
	function isGzString($value) {
		return @gzuncompress($value) !== FALSE;
	}
}
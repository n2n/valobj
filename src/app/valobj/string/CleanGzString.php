<?php

namespace valobj\string;

use n2n\spec\valobj\err\IllegalValueException;
use n2n\validation\validator\impl\ValidationUtils;
use n2n\util\StringUtils;
use n2n\util\ex\err\ConfigurationError;
use n2n\util\GzuncompressFailedException;
use n2n\util\ex\IllegalStateException;
use n2n\util\ex\ExUtils;

/**
 * Usually not used by its own but as super type of ever other String value object.
 * any Sub can extend CleanGzString and simply override the const, and it will use the constructor below
 * with either default params, or the given in that subclass
 * so for a label that allow only 16 Chars, simply set const MAX_LENGTH = 16;
 * gzuncompresses or gzcompresses gzstring.
 */

class CleanGzString extends StringValueObjectAdapter {
	const MIN_LENGTH = 1;
	const MAX_LENGTH = 100000;
	const SIMPLE_WHITESPACES_ONLY = false;

	public final function __construct(string $value) {
		if (static::MIN_LENGTH < 1) {
			throw new ConfigurationError('Illegal MIN_LENGTH constant defined in ' . static::class
					. '. Value must be at least 1.');
		}

		parent::__construct($value);

		try {
			$uncompressedValue = StringUtils::gzuncompress($this->value);
		} catch (GzuncompressFailedException $e) {
			throw new IllegalValueException('Value is no GZ String: ' . $this->value);
		}

		IllegalValueException::assertTrue(ValidationUtils::maxlength($uncompressedValue, static::MAX_LENGTH),
				'Value too long: ' . $uncompressedValue);
		IllegalValueException::assertTrue(ValidationUtils::minlength($uncompressedValue, static::MIN_LENGTH),
				'Value too short: ' . $uncompressedValue);
		IllegalValueException::assertTrue(StringUtils::isClean($uncompressedValue, static::SIMPLE_WHITESPACES_ONLY),
				'Value not clean: ' . $uncompressedValue);
	}

	function uncompress(): string {
		try {
			return StringUtils::gzuncompress($this->value);
		} catch (GzuncompressFailedException $e) {
			throw new IllegalStateException(static::class . ' holds an illegal gz value: ' . $this->value);
		}
	}

	/**
	 * Same as in {@link self::checkedFromUncompressed()} but throws unchecked exception on failure.
	 */
	static function fromUncompressed(?string $uncompressedString, bool $lenient = false): ?static {
		return ExUtils::try(fn () => static::checkedFromUncompressed($uncompressedString, $lenient));
	}

	/**
	 * @param bool $lenient if true uncompressedString will be striped of non-printable charcters and illegal whitespaces first.
	 * @throws IllegalValueException
	 */
	static function checkedFromUncompressed(?string $uncompressedString, bool $lenient = false): ?static {
		if ($uncompressedString === null || ($uncompressedString === '' && $lenient)) {
			return null;
		}
		if ($lenient) {
			$uncompressedString = StringUtils::clean(trim((string) $uncompressedString), static::SIMPLE_WHITESPACES_ONLY);
		}
		return new static(gzcompress($uncompressedString, 9));

	}
}
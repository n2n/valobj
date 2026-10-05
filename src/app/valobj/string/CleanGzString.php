<?php

namespace valobj\string;

use n2n\spec\valobj\err\IllegalValueException;
use n2n\validation\validator\impl\ValidationUtils;
use n2n\util\StringUtils;
use n2n\util\ex\ExUtils;
use n2n\util\ex\err\ConfigurationError;
use n2n\util\ex\NotYetImplementedException;

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

		IllegalValueException::assertTrue(static::isGzString($value),
				'Value is no GZ String: ' . $value);

		$uncompressedValue = $this->uncompress();

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

	static function isGzString($value): bool {
		return @gzuncompress($value) !== FALSE;
	}

	public static function from(string|\Stringable|null $value, bool $lenient = false): null|static {
		if ($value === null) {
			return null;
		}

		return static::fromUncompressed(
				StringUtils::clean(trim((string) $value), static::SIMPLE_WHITESPACES_ONLY));
	}

	public static function checkedFrom(string|\Stringable|null $value, bool $lenient = false): ?static {
		throw new NotYetImplementedException();
	}
}
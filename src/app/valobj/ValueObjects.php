<?php

namespace valobj;

use valobj\string\Email;
use n2n\util\ex\ExUtils;
use valobj\string\Name;
use valobj\string\LongLabel;
use valobj\string\Text;
use valobj\int\NbId;
use valobj\float\TypicalTwoDigitDecimal;
use valobj\string\ColorHex;
use valobj\string\ShortLabel;
use valobj\int\PositiveInt;
use valobj\int\NbIdArray;
use valobj\string\MediumLabel;
use valobj\string\LatinString;

class ValueObjects {

	static function email(string $email): Email {
		return ExUtils::try(fn () => new Email($email));
	}

	static function name(string $value): Name {
		return ExUtils::try(fn () => new Name($value));
	}

	static function shortLabel(string $value): ShortLabel {
		return ExUtils::try(fn () => new ShortLabel($value));
	}

	static function mediumLabel(string $value): MediumLabel {
		return ExUtils::try(fn () => new MediumLabel($value));
	}

	static function longLabel(string $value): LongLabel {
		return ExUtils::try(fn () => new LongLabel($value));
	}

	static function text(string $value): Text {
		return ExUtils::try(fn () => new Text($value));
	}

	static function positiveInt(int $value): PositiveInt {
		return ExUtils::try(fn () => new PositiveInt($value));
	}

	static function nbId(int $value): NbId {
		return ExUtils::try(fn () => new NbId($value));
	}

	static function nbIdArray(array $nbIds): NbIdArray {
		return ExUtils::try(fn () => new NbIdArray($nbIds));
	}

	static function colorHex(string $value): ColorHex {
		return ExUtils::try(fn () => new ColorHex($value));
	}

	static function latinString(string $value): LatinString {
		return ExUtils::try(fn () => new LatinString($value));
	}

//	static function title(string $value): Title {
//		return ExUtils::try(fn () => new Title($value));
//	}

//	static function typicalTwoDigitDecimal(float $value): TypicalTwoDigitDecimal {
//		return ExUtils::try(fn () => new TypicalTwoDigitDecimal($value));
//	}
}
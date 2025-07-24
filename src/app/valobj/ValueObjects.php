<?php

namespace valobj;

use valobj\string\Email;
use n2n\util\ex\ExUtils;
use valobj\string\Name;
use valobj\string\LongLabel;
use valobj\string\Text;

class ValueObjects {

	static function email(string $email): Email {
		return ExUtils::try(fn () => new Email($email));
	}

	static function name(string $value): Name {
		return ExUtils::try(fn () => new Name($value));
	}

	static function longLabel(string $value): LongLabel {
		return ExUtils::try(fn () => new LongLabel($value));}

	static function text(string $value): Text {
		return ExUtils::try(fn () => new Text($value));
	}

//	static function title(string $value): Title {
//		return ExUtils::try(fn () => new Title($value));
//	}
}
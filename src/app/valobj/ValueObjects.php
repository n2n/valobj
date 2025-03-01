<?php

namespace valobj;

use valobj\string\Email;
use n2n\util\ex\ExUtils;
use valobj\string\Name;

class ValueObjects {

	static function email(string $email): Email {
		return ExUtils::try(fn () => new Email($email));
	}

	static function name(string $value): Name {
		return ExUtils::try(fn () => new Name($value));
	}
}
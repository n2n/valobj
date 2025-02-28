<?php

namespace valobj;

use valobj\string\Email;
use n2n\util\ex\ExUtils;

class ValueObjects {

	static function email(string $email): Email {
		return ExUtils::try(fn () => new Email($email));
	}
}
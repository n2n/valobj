<?php

namespace n2n\valobj\impl;

use n2n\valobj\impl\string\Email;
use n2n\util\ex\ExUtils;

class ValueObjects {

	static function email(string $email): Email {
		return ExUtils::try(fn () => new Email($email));
	}
}
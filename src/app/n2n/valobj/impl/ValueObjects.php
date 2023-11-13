<?php

namespace n2n\valobj\impl;

use n2n\valobj\impl\string\Email;
use n2n\util\valobj\IncompatibleValueException;

class ValueObjects {

	/**
	 * @throws IncompatibleValueException
	 */
	static function email(string $email): Email {
		return new Email($email);
	}
}
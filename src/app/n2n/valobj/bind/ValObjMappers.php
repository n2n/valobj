<?php

namespace n2n\valobj\bind;

use n2n\valobj\impl\string\Email;
use n2n\bind\mapper\Mapper;

class ValObjMappers {

	static function email(bool $mandatory): Mapper {
		return Email::defaultMapper($mandatory);
	}
}
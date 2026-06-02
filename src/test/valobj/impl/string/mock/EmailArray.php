<?php

namespace valobj\impl\string\mock;

use valobj\string\StringValueObjectArrayAdapter;
use n2n\util\col\attribute\ValueType;
use valobj\string\Email;

#[ValueType(Email::class)]
class EmailArray extends StringValueObjectArrayAdapter {

}
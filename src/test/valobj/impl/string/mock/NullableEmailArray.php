<?php

namespace valobj\impl\string\mock;

use valobj\string\NullableStringValueObjectArrayAdapter;
use n2n\util\col\attribute\ValueType;
use valobj\string\Email;

#[ValueType(Email::class, nullable: true)]
class NullableEmailArray extends NullableStringValueObjectArrayAdapter {

}

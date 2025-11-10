<?php

namespace valobj\int;

use n2n\util\col\attribute\ValueType;

#[ValueType(PositiveInt::class)]
class PositiveIntArray extends IntValueObjectArrayAdapter {
}
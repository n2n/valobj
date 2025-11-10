<?php

namespace valobj\int;

use n2n\util\col\attribute\ValueType;

/**
 * @extends IntValueObjectArrayAdapter<scalar, PositiveInt>
 */
#[ValueType(PositiveInt::class)]
class PositiveIntArray extends IntValueObjectArrayAdapter {
}
<?php

namespace valobj\float;

use n2n\spec\valobj\scalar\FloatValueObject;

abstract class FloatValueObjectAdapter implements FloatValueObject, \Stringable, \JsonSerializable {
	use FloatValueObjectAdapterTrait;
}
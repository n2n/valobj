<?php

namespace valobj\int;

use n2n\spec\valobj\err\IllegalValueException;
use n2n\bind\attribute\impl\Marshal;
use n2n\bind\mapper\Mapper;
use n2n\bind\mapper\impl\Mappers;
use n2n\bind\attribute\impl\Unmarshal;
use n2n\util\col\attribute\ValueType;

#[ValueType(NbId::class)]
class NbIdArray extends PositiveIntArray {
}
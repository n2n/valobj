<?php

namespace valobj\string;

use n2n\bind\attribute\impl\Marshal;
use n2n\bind\mapper\Mapper;
use n2n\bind\mapper\impl\Mappers;
use n2n\bind\attribute\impl\Unmarshal;
use n2n\util\col\TypedArray;
use n2n\util\col\attribute\ValueType;
use n2n\util\col\CollectionTypeUtils;
use ReflectionClass;
use n2n\spec\valobj\scalar\StringValueObject;
use n2n\validation\validator\impl\Validators;

/**
 * @template K
 * @template V
 * @extends TypedArray<K, V>
 */
#[ValueType(StringValueObject::class)]
abstract class StringValueObjectArrayAdapter extends NullableStringValueObjectArrayAdapter {

}
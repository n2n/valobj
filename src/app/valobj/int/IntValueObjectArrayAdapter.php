<?php

namespace valobj\int;

use n2n\bind\attribute\impl\Marshal;
use n2n\bind\mapper\Mapper;
use n2n\bind\mapper\impl\Mappers;
use n2n\bind\attribute\impl\Unmarshal;
use n2n\util\col\TypedArray;
use n2n\util\col\attribute\ValueType;
use n2n\util\col\CollectionTypeUtils;
use ReflectionClass;
use n2n\spec\valobj\scalar\IntValueObject;
use n2n\validation\validator\impl\Validators;

/**
 * @template K
 * @template V
 * @extends TypedArray<K, V>
 */
#[ValueType(IntValueObject::class)]
abstract class IntValueObjectArrayAdapter extends TypedArray {

	#[Marshal]
	static function marshalMapper(): Mapper {
		return Mappers::valueClosure(fn (IntValueObjectArrayAdapter $ints) => array_map(
				fn (IntValueObject $int) => $int->toScalar(),
				$ints->toArray()));
	}

	#[Unmarshal]
	static function unmarshalMapper(): Mapper {
		$class = new \ReflectionClass(static::class);
		$namedTypeConstraint = CollectionTypeUtils::detectValueTypeConstraint(new ReflectionClass(static::class));

		return Mappers::pipe(
				Mappers::subForeach(
						Mappers::unmarshal($namedTypeConstraint->getTypeName()),
						Validators::mandatoryIf(!$namedTypeConstraint->allowsNull())),
				Mappers::subMerge(),
				Mappers::valueClosure(fn (array $intValueObjects) => $class->newInstance($intValueObjects)));
	}
}
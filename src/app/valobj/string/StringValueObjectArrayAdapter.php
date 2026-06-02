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
abstract class StringValueObjectArrayAdapter extends TypedArray {

	#[Marshal]
	static function marshalMapper(): Mapper {
		return Mappers::value(fn (StringValueObjectArrayAdapter $strings) => array_map(
				fn (StringValueObject $string) => $string->toScalar(),
				$strings->toArray()));
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
				Mappers::value(fn (array $stringValueObjects) => $class->newInstance($stringValueObjects)));
	}
}
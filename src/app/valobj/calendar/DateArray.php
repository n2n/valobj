<?php

namespace valobj\calendar;

use n2n\bind\attribute\impl\Marshal;
use n2n\bind\mapper\Mapper;
use n2n\bind\mapper\impl\Mappers;
use n2n\bind\attribute\impl\Unmarshal;
use n2n\util\col\TypedArray;
use n2n\util\col\attribute\ValueType;
use n2n\util\col\CollectionTypeUtils;
use n2n\util\calendar\Date;
use n2n\util\DateUtils;
use ReflectionClass;
use n2n\validation\validator\impl\Validators;
use n2n\util\DateParseException;
use n2n\validation\lang\ValidationMessages;

/**
 * @extends TypedArray<scalar, Date>
 */
#[ValueType(Date::class)]
class DateArray extends TypedArray {

	#[Marshal]
	static function marshalMapper(): Mapper {
		return Mappers::valueClosure(fn (DateArray $dates) => array_map(
				fn (Date $date) => DateUtils::dateToSql($date),
				$dates->toArray()));
	}

	#[Unmarshal]
	static function unmarshalMapper(): Mapper {
		return Mappers::pipe(
				Mappers::subForeach(
						Mappers::bindableNotNullClosure(function ($bindable) {
							try {
								$bindable->setValue(new Date($bindable->getValue()));
							} catch (DateParseException $e) {
								$bindable->addError(ValidationMessages::invalid());
								$bindable->setDirty(true);
							}
						}),
						Validators::mandatoryIf(!$namedTypeConstraint->allowsNull())),
				Mappers::subMerge(),
				Mappers::valueClosure(fn (array $dates) => $class->newInstance($dates)));
	}
}


<?php

namespace valobj\calendar;

use n2n\bind\attribute\impl\Marshal;
use n2n\bind\mapper\Mapper;
use n2n\bind\mapper\impl\Mappers;
use n2n\bind\attribute\impl\Unmarshal;
use n2n\util\col\TypedArray;
use n2n\util\col\attribute\ValueType;
use n2n\util\calendar\Date;
use n2n\util\DateUtils;

/**
 * @extends TypedArray<scalar, Date>
 */
#[ValueType(Date::class)]
class DateArray extends TypedArray {

	#[Marshal]
	static function marshalMapper(): Mapper {
		return Mappers::value(fn (DateArray $dates) => array_map(
				fn (Date $date) => DateUtils::dateToSql($date),
				$dates->toArray()));
	}

	#[Unmarshal]
	static function unmarshalMapper(): Mapper {
		$class = new \ReflectionClass(static::class);
		return Mappers::pipe(
				Mappers::subForeach(Mappers::date(true)),
				Mappers::subMerge(),
				Mappers::value(fn (array $dates) => $class->newInstance($dates)));
	}
}


<?php

namespace valobj\impl\calendar;

use n2n\bind\err\BindTargetException;
use n2n\bind\err\BindMismatchException;
use n2n\bind\err\UnresolvableBindableException;
use n2n\bind\build\impl\Bind;
use n2n\bind\mapper\impl\Mappers;
use valobj\int\NbId;
use valobj\ValueObjects;
use n2n\validation\plan\ErrorMap;
use valobj\int\NbIdArray;
use PHPUnit\Framework\TestCase;
use n2n\util\calendar\Date;
use valobj\calendar\DateArray;

class DateArrayTest extends TestCase {


	/**
	 * @throws BindTargetException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshal(): void {
		$result = Bind::attrs(['key1' => ['2023-01-01', '2024-04-04']])
				->prop('key1', Mappers::unmarshal(DateArray::class))
				->toArray()
				->exec();

		$this->assertEquals(
				[new Date('2023-01-01'), new Date('2024-04-04')],
				$result->get()['key1']->toArray());
	}

	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testUnmarshalValFail(): void {
		$result = Bind::attrs(['key1' => ['2023-32-32', null]])
				->prop('key1', Mappers::unmarshal(DateArray::class))
				->toArray()
				->exec();

		$this->assertFalse($result->isValid());
		$errorMap = $result->getErrorMap();
		$this->assertTrue(assert($errorMap instanceof ErrorMap));
		$this->assertEquals('Invalid', (string) $errorMap->getAllMessages()[0]);
	}
}
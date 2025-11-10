<?php

namespace valobj\impl\int;

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

class NbIdArrayTest extends TestCase {


	/**
	 * @throws BindTargetException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshal(): void {
		$result = Bind::attrs(['key1' => [1, 2]])
				->prop('key1', Mappers::unmarshal(NbIdArray::class))
				->toArray()
				->exec();

		$this->assertEquals(
				[ValueObjects::nbId(1), ValueObjects::nbId(2)],
				$result->get()['key1']->toArray());
	}

	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testUnmarshalValFail(): void {
		$result = Bind::attrs(['key1' => [0, 1]])
				->prop('key1', Mappers::unmarshal(NbIdArray::class))
				->toArray()
				->exec();

		$this->assertFalse($result->isValid());
		$errorMap = $result->getErrorMap();
		$this->assertTrue(assert($errorMap instanceof ErrorMap));
		$this->assertEquals('Min [min = 1]', (string) $errorMap->getAllMessages()[0]);
	}
}
<?php

namespace valobj\impl\string;

use n2n\bind\err\BindTargetException;
use n2n\bind\err\BindMismatchException;
use n2n\bind\err\UnresolvableBindableException;
use n2n\bind\build\impl\Bind;
use n2n\bind\mapper\impl\Mappers;
use n2n\validation\plan\ErrorMap;
use valobj\impl\string\mock\EmailArray;
use valobj\string\Email;
use PHPUnit\Framework\TestCase;

class StringValueObjectArrayAdapterTest extends TestCase {

	/**
	 * @throws BindTargetException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshal(): void {
		$result = Bind::attrs(['key1' => ['paul@holeradio.ch', 'super-paul@holeradio.ch']])
				->prop('key1', Mappers::unmarshal(EmailArray::class))
				->toArray()
				->exec();

		$this->assertEquals(
				[Email::from('paul@holeradio.ch'), Email::from('super-paul@holeradio.ch')],
				$result->get()['key1']->toArray());
	}

	/**
	 * @throws BindTargetException
	 * @throws UnresolvableBindableException
	 * @throws BindMismatchException
	 */
	function testUnmarshalValFail(): void {
		$result = Bind::attrs(['key1' => ['paul(at)holeradio.ch', 'super-paul@holeradio.ch']])
				->prop('key1', Mappers::unmarshal(EmailArray::class))
				->toArray()
				->exec();

		$this->assertFalse($result->isValid());
		$errorMap = $result->getErrorMap();
		$this->assertTrue(assert($errorMap instanceof ErrorMap));
		$this->assertEquals('Email', (string) $errorMap->getAllMessages()[0]);
	}
}
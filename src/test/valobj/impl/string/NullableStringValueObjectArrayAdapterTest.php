<?php

namespace valobj\impl\string;

use n2n\bind\err\BindTargetException;
use n2n\bind\err\BindMismatchException;
use n2n\bind\err\UnresolvableBindableException;
use n2n\bind\build\impl\Bind;
use n2n\bind\mapper\impl\Mappers;
use valobj\impl\string\mock\NullableEmailArray;
use valobj\string\Email;
use PHPUnit\Framework\TestCase;

class NullableStringValueObjectArrayAdapterTest extends TestCase {

	/**
	 * @throws BindTargetException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testMarshalWithNull(): void {
		$emails = new NullableEmailArray([Email::from('paul@holeradio.ch'), null]);

		$result = Bind::attrs(['key1' => $emails])
				->prop('key1', Mappers::marshal())
				->toArray()
				->exec();

		$this->assertTrue($result->isValid());
		$this->assertEquals(['paul@holeradio.ch', null], $result->get()['key1']);
	}

	/**
	 * @throws BindTargetException
	 * @throws BindMismatchException
	 * @throws UnresolvableBindableException
	 */
	function testUnmarshalWithNull(): void {
		$result = Bind::attrs(['key1' => ['paul@holeradio.ch', null]])
				->prop('key1', Mappers::unmarshal(NullableEmailArray::class))
				->toArray()
				->exec();

		$this->assertTrue($result->isValid());
		$this->assertEquals([Email::from('paul@holeradio.ch'), null], $result->get()['key1']->toArray());
	}

	function testJsonSerializeWithNull(): void {
		$emails = new NullableEmailArray([Email::from('paul@holeradio.ch'), null]);
		$this->assertEquals('["paul@holeradio.ch",null]', json_encode($emails));
	}
}

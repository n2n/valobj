<?php

namespace valobj\impl\string;

use PHPUnit\Framework\TestCase;
use n2n\spec\valobj\err\IllegalValueException;
use valobj\ValueObjects;
use n2n\bind\build\impl\Bind;
use n2n\bind\mapper\impl\Mappers;
use n2n\bind\err\BindTargetException;
use n2n\bind\err\BindMismatchException;
use n2n\bind\err\UnresolvableBindableException;
use n2n\validation\plan\ErrorMap;
use valobj\string\Email;
use valobj\string\Text;

class StringValueObjectAdapterTest extends TestCase {

	function testEquals(): void {
		$this->assertTrue((new Email('holeradio@huii.ch'))->equals(new Email('holeradio@huii.ch')));
		$this->assertFalse((new Email('holeradio@super-huii.ch'))->equals(new Email('holeradio@huii.ch')));

		$this->assertTrue((new Email('holeradio@huii.ch'))->equals('holeradio@huii.ch'));
		$this->assertFalse((new Email('holeradio@super-huii.ch'))->equals('holeradio@huii.ch'));

		$this->assertFalse((new Email('holeradio@super-huii.ch'))->equals(null));
	}

	function testEmptyString(): void {
		$this->expectException(IllegalValueException::class);
		$this->expectExceptionMessage('Empty string not allowed.');

		new Text('');
	}
}
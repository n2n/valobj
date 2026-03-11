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
use valobj\string\CleanString;
use n2n\util\uri\Url;
use n2n\util\ex\IllegalStateException;
use valobj\string\ShortLabel;

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

	/**
	 * @throws IllegalValueException
	 */
	function testFrom() {
		$this->assertEquals('holeradio@huii.ch', Email::from('holeradio@huii.ch'));
		$this->assertEquals('https:://www.hnm.ch',
				CleanString::from(Url::create('https:://www.hnm.ch')));
		$this->assertNull(CleanString::from(null));

		$this->expectException(IllegalStateException::class);
		ShortLabel::from(str_repeat('a', ShortLabel::MAX_LENGTH + 1));
	}
}
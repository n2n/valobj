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

class NameTest extends TestCase {


	function testConstruct(): void {
		$email = ValueObjects::name('Testerich');

		$this->assertEquals('Testerich', $email->toScalar());
	}

}
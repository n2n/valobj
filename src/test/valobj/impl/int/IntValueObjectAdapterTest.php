<?php

namespace valobj\impl\int;

use valobj\ValueObjects;
use n2n\util\ex\IllegalStateException;
use valobj\int\NbId;
use PHPUnit\Framework\TestCase;
use n2n\bind\err\BindTargetException;
use n2n\bind\err\BindMismatchException;
use n2n\bind\err\UnresolvableBindableException;
use n2n\bind\build\impl\Bind;
use n2n\bind\mapper\impl\Mappers;
use n2n\validation\plan\ErrorMap;
use valobj\impl\int\mock\SubNbId;
use n2n\spec\valobj\err\IllegalValueException;

class IntValueObjectAdapterTest extends TestCase {

	function testFrom(): void {

		$nbId = NbId::from(1);
		$otherNbId = NbId::from($nbId);

		$this->assertNotSame($nbId, $otherNbId);
		$this->assertEquals($nbId, $otherNbId);
	}

}
<?php

namespace n2n\valobj\bind;

use n2n\bind\mapper\impl\MapperAdapter;
use n2n\bind\plan\BindableBoundary;
use n2n\bind\plan\BindContext;
use n2n\util\magic\MagicContext;
use n2n\reflection\magic\MagicMethodInvoker;
use n2n\bind\mapper\impl\SingleMapperAdapter;
use n2n\bind\plan\Bindable;

class ValueObjectMapper extends SingleMapperAdapter {

	function __construct(private string $valueObjectClassName) {

	}

	function mapSingle(Bindable $bindable, BindContext $bindContext, MagicContext $magicContext): bool {
		$value = $bindable->getValue();

		if ($value === null) {
			return true;
		}

		new SingleValueObjectAnalyzer($)
		$this->readSafeValue()
		$magicMethodInvoker = new MagicMethodInvoker();
		$magicMethodInvoker->
	}




		$this->readSafeValue()
	}
}
<?php

namespace n2n\valobj\attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class BindProfile {

	function __construct(public ?string $name = null) {

	}
}
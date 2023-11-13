<?php

namespace n2n\valobj\bind;

use n2n\bind\mapper\Mapper;
use n2n\util\ex\err\ConfigurationError;
use n2n\reflection\magic\MagicMethodInvoker;
use n2n\valobj\attribute\BindProfile;
use n2n\util\type\TypeConstraint;

class SingleValueObjectAnalyzer {


	function __construct(private \ReflectionClass $valueObjectClass) {

	}

	/**
	 * Returns the TypeConstraint of the first parameter of the constructor.
	 *
	 * @return TypeConstraint
	 * @throws InvalidValueObjectException if constructor has no parameters,
	 * 		if the first parameter of the constructor is not typed.
	 */
	function extractValueTypeConstraint(): TypeConstraint {
		throw new ConfigurationError();
	}

	/**
	 * Looks for a method annotated with {@link BindProfile} and uses {@link MagicMethodInvoker}
	 * to call it and make sure, it returns and array of {@link Mapper}
	 *
	 * @param string|null $profileName must match {@link BindProfile::$name}
	 * @return Mapper[]|null null if no matching method annotated with BindProfile was found.
	 * @throws ConfigurationError if method annotated with BindProfile is not static or there are multiple BindProfiles
	 *	with the passed profile name.
	 *
	 */
	function extractMappers(?string $profileName): ?array {

	}

}
<?php

namespace valobj\string\crypt;

use valobj\string\StringValueObjectAdapter;
use n2n\util\crypt\symmetric\EncryptedSecret;
use n2n\util\crypt\symmetric\SymmetricCryptUtils;
use n2n\util\type\attrs\AttributesException;
use n2n\util\crypt\ex\DecryptionFailedException;
use n2n\spec\valobj\err\IllegalValueException;
use n2n\util\crypt\PlainSecret;

class EnvEncryptedSecret extends StringValueObjectAdapter {

	const KEY_ENVIRONMENT_VARIABLE_NAME = 'SECRET_ENCRYPTION_KEY';

	private PlainSecret $plainSecret;

	function __construct(string $value) {
		parent::__construct($value);

		try {
			$this->plainSecret = SymmetricCryptUtils::decrypt(EncryptedSecret::fromJson($value));
		} catch (AttributesException|DecryptionFailedException $e) {
			throw new IllegalValueException($e->getMessage(), previous: $e);
		}
	}




}
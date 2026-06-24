<?php

namespace valobj\impl\string\mock;

use valobj\string\crypt\EnvEncryptedSecret;

class SubEnvEncryptedSecret extends EnvEncryptedSecret{

	const KEY_ENVIRONMENT_VARIABLE_NAME = 'SUB_SECRET_ENCRYPTION_KEY';
}
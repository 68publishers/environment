<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Debug;

use SensitiveParameter;
use function is_string;
use function mb_strlen;
use function hash_equals;

/**
 * The debug mode is enabled if a cookie with the secret exists.
 * A missing, empty or short secret (fewer characters than $minimumSecretLength) never enables the debug mode.
 */
final class SecretCookieDetector implements DebugModeDetectorInterface
{
	public const DEFAULT_MINIMUM_SECRET_LENGTH = 32;

	public function __construct(
		#[SensitiveParameter]
		private readonly ?string $secret,
		private readonly string $name = 'app-debug',
		private readonly int $minimumSecretLength = self::DEFAULT_MINIMUM_SECRET_LENGTH,
	) {
	}

	public function detect(): bool
	{
		if (NULL === $this->secret || '' === $this->secret || $this->minimumSecretLength > mb_strlen($this->secret, 'UTF-8')) {
			return FALSE;
		}

		$cookie = $_COOKIE[$this->name] ?? NULL;

		# a cookie "name[]=value" is an array and hash_equals() accepts strings only
		return is_string($cookie) && hash_equals($this->secret, $cookie);
	}
}

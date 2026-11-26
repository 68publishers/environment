<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Debug;

use SensitiveParameter;
use function is_string;
use function hash_equals;

/**
 * @deprecated Use SecretCookieDetector, it requires a minimal length of the secret.
 */
final class SimpleCookieDetector implements DebugModeDetectorInterface
{
	private string $value;

	private string $name;

	public function __construct(
		#[SensitiveParameter]
		string $value,
		string $name = 'app-debug',
	) {
		$this->value = $value;
		$this->name = $name;
	}

	public function detect(): bool
	{
		$cookie = $_COOKIE[$this->name] ?? NULL;

		return '' !== $this->value && is_string($cookie) && hash_equals($this->value, $cookie);
	}
}

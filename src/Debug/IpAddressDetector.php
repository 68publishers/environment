<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Debug;

use function in_array;
use function is_string;

final class IpAddressDetector implements DebugModeDetectorInterface
{
	/** @var array<string> */
	private array $list;

	private ?string $cookieName;

	/**
	 * @param array<string> $list
	 */
	public function __construct(array $list, ?string $cookieName = 'app-debug')
	{
		$this->list = $list;
		$this->cookieName = $cookieName;
	}

	public function detect(): bool
	{
		$address = $_SERVER['REMOTE_ADDR'] ?? NULL;

		if (in_array($address, $this->list, TRUE)) {
			return TRUE;
		}

		if (NULL === $this->cookieName) {
			return FALSE;
		}

		$cookie = is_string($_COOKIE[$this->cookieName] ?? NULL) ? $_COOKIE[$this->cookieName] : NULL;

		return NULL !== $cookie && in_array($cookie . '@' . $address, $this->list, TRUE);
	}
}

<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Debug;

final class IpAddressDetector implements IDebugModeDetector
{
	/** @var array  */
	private $list;

	/** @var string  */
	private $cookieName;

	/**
	 * @param array       $list
	 * @param string|NULL $cookieName
	 */
	public function __construct(array $list, ?string $cookieName = 'app-debug')
	{
		$this->list = $list;
		$this->cookieName = $cookieName;
	}

	/**************** interface SixtyEightPublishers\Environment\Debug\IDebugModeDetector ****************[

	/**
	 * {@inheritDoc}
	 */
	public function detect(): bool
	{
		$address = $_SERVER['REMOTE_ADDR'] ?? php_uname('n');

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

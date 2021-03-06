<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Debug;

final class SimpleCookieDetector implements DebugModeDetectorInterface
{
	/** @var string  */
	private $value;

	/** @var string  */
	private $name;

	/**
	 * @param string $value
	 * @param string $name
	 */
	public function __construct(string $value, string $name = 'app-debug')
	{
		$this->value = $value;
		$this->name = $name;
	}

	/**************** interface SixtyEightPublishers\Environment\Debug\IDebugModeDetector ****************[

	/**
	 * {@inheritDoc}
	 */
	public function detect(): bool
	{
		return $this->value === ($_COOKIE[$this->name] ?? NULL);
	}
}

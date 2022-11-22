<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Debug;

final class SimpleCookieDetector implements DebugModeDetectorInterface
{
	private string $value;

	private string $name;

	public function __construct(string $value, string $name = 'app-debug')
	{
		$this->value = $value;
		$this->name = $name;
	}

	public function detect(): bool
	{
		return $this->value === ($_COOKIE[$this->name] ?? NULL);
	}
}

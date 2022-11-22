<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Debug;

use function in_array;
use function array_search;
use const PHP_SAPI;

final class CliOptionDetector implements DebugModeDetectorInterface
{
	private string $optionName;

	public function __construct(string $optionName = 'app-debug')
	{
		$this->optionName = $optionName;
	}

	public function detect(): bool
	{
		if ('cli' !== PHP_SAPI) {
			return FALSE;
		}

		$option = '--' . $this->optionName;

		if (in_array($option, $_SERVER['argv'] ?? [], TRUE)) {
			unset($_SERVER['argv'][array_search($option, $_SERVER['argv'], TRUE)]);

			return TRUE;
		}

		return FALSE;
	}
}

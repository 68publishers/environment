<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Debug;

final class CliOptionDetector implements DebugModeDetectorInterface
{
	/** @var string  */
	private $optionName;

	/**
	 * @param string $optionName
	 */
	public function __construct(string $optionName = 'app-debug')
	{
		$this->optionName = $optionName;
	}

	/**************** interface SixtyEightPublishers\Environment\Debug\IDebugModeDetector ****************[

	/**
	 * {@inheritDoc}
	 */
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

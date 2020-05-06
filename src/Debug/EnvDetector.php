<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Debug;

use SixtyEightPublishers;

final class EnvDetector implements IDebugModeDetector
{
	/**************** interface SixtyEightPublishers\Environment\Debug\IDebugModeDetector ****************[

	/**
	 * {@inheritDoc}
	 */
	public function detect(): bool
	{
		$debug = $_SERVER[SixtyEightPublishers\Environment\Bootstrap\EnvBootstrap::APP_DEBUG] ?? $_ENV[SixtyEightPublishers\Environment\Bootstrap\EnvBootstrap::APP_DEBUG] ?? 'prod' !== $_SERVER[SixtyEightPublishers\Environment\Bootstrap\EnvBootstrap::APP_ENV];

		return (bool) $debug || (bool) filter_var($debug, FILTER_VALIDATE_BOOLEAN);
	}
}

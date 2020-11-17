<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Debug;

use SixtyEightPublishers\Environment\Bootstrap\EnvBootstrap;

final class EnvDetector implements DebugModeDetectorInterface
{
	/**************** interface SixtyEightPublishers\Environment\Debug\IDebugModeDetector ****************[

	/**
	 * {@inheritDoc}
	 */
	public function detect(): bool
	{
		$debug = $_SERVER[EnvBootstrap::APP_DEBUG] ?? $_ENV[EnvBootstrap::APP_DEBUG] ?? 'prod' !== $_SERVER[EnvBootstrap::APP_ENV];

		return (bool) $debug || (bool) filter_var($debug, FILTER_VALIDATE_BOOLEAN);
	}
}

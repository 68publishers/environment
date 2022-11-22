<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Debug;

use SixtyEightPublishers\Environment\Bootstrap\EnvBootstrap;
use function filter_var;

final class EnvDetector implements DebugModeDetectorInterface
{
	public function detect(): bool
	{
		$debug = $_SERVER[EnvBootstrap::APP_DEBUG] ?? $_ENV[EnvBootstrap::APP_DEBUG] ?? 'prod' !== ($_SERVER[EnvBootstrap::APP_ENV] ?? NULL);

		return filter_var($debug, FILTER_VALIDATE_BOOLEAN);
	}
}

<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Debug;

interface DebugModeDetectorInterface
{
	/**
	 * @return bool
	 */
	public function detect(): bool;
}

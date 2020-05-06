<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Debug;

interface IDebugModeDetector
{
	/**
	 * @return bool
	 */
	public function detect(): bool;
}

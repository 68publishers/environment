<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Debug;

interface DebugModeDetectorInterface
{
	public function detect(): bool;
}

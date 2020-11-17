<?php

declare(strict_types=1);

use SixtyEightPublishers\Environment\Helper\EnvAccessor;

if (!function_exists('env')) {
	function env(string $env, $default = NULL)
	{
		$parts = explode('|', $env);

		return 1 === func_num_args()
			? EnvAccessor::getEnv(array_shift($parts), $parts)
			: EnvAccessor::getEnv(array_shift($parts), $parts, $default);
	}
}

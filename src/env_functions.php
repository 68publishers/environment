<?php

declare(strict_types=1);

if (!function_exists('env')) {
	function env(string $env, $default = NULL)
	{
		$parts = explode('|', $env);

		return 1 === func_num_args()
			? SixtyEightPublishers\Environment\Helper\EnvAccessor::getEnv(array_shift($parts), $parts)
			: SixtyEightPublishers\Environment\Helper\EnvAccessor::getEnv(array_shift($parts), $parts, $default);
	}
}

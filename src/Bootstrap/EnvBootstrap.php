<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Bootstrap;

use RuntimeException;
use Nette\Configurator;
use Symfony\Component\Dotenv\Dotenv;

final class EnvBootstrap
{
	public const    APP_ENV = 'APP_ENV',
					APP_DEBUG = 'APP_DEBUG';

	/**
	 * @throws \RuntimeException
	 */
	public function __construct()
	{
		throw new RuntimeException(sprintf(
			'Class %s can\'t be initialized via the constructor.',
			static::class
		));
	}

	/**
	 * @param string                                                               $rootDir
	 * @param \SixtyEightPublishers\Environment\Debug\DebugModeDetectorInterface[] $debugModeDetectors
	 *
	 * @return array
	 */
	public static function boot(string $rootDir, array $debugModeDetectors = []): array
	{
		self::loadEnv($rootDir);
		self::detectDebugMode($debugModeDetectors);

		return $_ENV;
	}

	/**
	 * @param \Nette\Configurator $configurator
	 * @param string              $rootDir
	 * @param array               $debugModeDetectors
	 *
	 * @return array
	 */
	public static function bootNetteConfigurator(Configurator $configurator, string $rootDir, array $debugModeDetectors = []): array
	{
		$env = self::boot($rootDir, $debugModeDetectors);

		$configurator->setDebugMode((bool) $env[self::APP_DEBUG]);
		$configurator->addDynamicParameters([
			'env' => $env,
		]);

		return $env;
	}

	/**
	 * @param string $rootDir
	 *
	 * @return void
	 * @throws \RuntimeException
	 */
	public static function loadEnv(string $rootDir): void
	{
		if (is_array($env = @include $rootDir . '/.env.local.php') && ($_SERVER[self::APP_ENV] ?? $_ENV[self::APP_ENV] ?? $env[self::APP_ENV]) === $env[self::APP_ENV]) {
			foreach ($env as $k => $v) {
				$_ENV[$k] = $_ENV[$k] ?? (isset($_SERVER[$k]) && 0 !== strpos($k, 'HTTP_') ? $_SERVER[$k] : $v);
			}

			return;
		}

		if (!class_exists(Dotenv::class)) {
			throw new RuntimeException('Please required package symfony/dotenv.');
		}

		(new Dotenv(FALSE))->loadEnv($rootDir . '/.env');

		$_SERVER += $_ENV;
		$_SERVER[self::APP_ENV] = $_ENV[self::APP_ENV] = ($_SERVER[self::APP_ENV] ?? $_ENV[self::APP_ENV] ?? NULL) ?: 'dev';
	}

	/**
	 * @param array $debugModeDetectors
	 *
	 * @return bool
	 */
	public static function detectDebugMode(array $debugModeDetectors = []): bool
	{
		$debug = FALSE;

		foreach ($debugModeDetectors as $debugModeDetector) {
			if (TRUE === $debugModeDetector->detect()) {
				$debug = TRUE;

				break;
			}
		}

		$_SERVER[self::APP_DEBUG] = $_ENV[self::APP_DEBUG] = $debug ? '1' : '0';

		return $debug;
	}
}

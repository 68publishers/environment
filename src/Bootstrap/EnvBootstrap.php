<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Bootstrap;

use Nette;
use Symfony;

final class EnvBootstrap
{
	public const    APP_ENV = 'APP_ENV',
					APP_DEBUG = 'APP_DEBUG';

	public function __construct()
	{
		throw new \RuntimeException(sprintf(
			'Class %s can\'t be initialized via the constructor.',
			static::class
		));
	}

	/**
	 * @param string                                                       $rootDir
	 * @param \SixtyEightPublishers\Environment\Debug\IDebugModeDetector[] $debugModeDetectors
	 *
	 * @return array
	 */
	public static function boot(string $rootDir, array $debugModeDetectors = []): array
	{
		self::loadEnv($rootDir);

		$_SERVER += $_ENV;
		$_SERVER[self::APP_ENV] = $_ENV[self::APP_ENV] = ($_SERVER[self::APP_ENV] ?? $_ENV[self::APP_ENV] ?? NULL) ?: 'dev';

		$debug = FALSE;

		foreach ($debugModeDetectors as $debugModeDetector) {
			if (TRUE === $debugModeDetector->detect()) {
				$debug = TRUE;

				break;
			}
		}

		$_SERVER[self::APP_DEBUG] = $_ENV[self::APP_DEBUG] = $debug ? '1' : '0';

		return $_ENV;
	}

	/**
	 * @param \Nette\Configurator $configurator
	 * @param string              $rootDir
	 * @param array               $debugModeDetectors
	 *
	 * @return array
	 */
	public static function bootNetteConfigurator(Nette\Configurator $configurator, string $rootDir, array $debugModeDetectors = []): array
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
	private static function loadEnv(string $rootDir): void
	{
		if (is_array($env = @include $rootDir . '/.env.local.php') && ($_SERVER[self::APP_ENV] ?? $_ENV[self::APP_ENV] ?? $env[self::APP_ENV]) === $env[self::APP_ENV]) {
			foreach ($env as $k => $v) {
				$_ENV[$k] = $_ENV[$k] ?? (isset($_SERVER[$k]) && 0 !== strpos($k, 'HTTP_') ? $_SERVER[$k] : $v);
			}

			return;
		}

		if (!class_exists(Symfony\Component\Dotenv\Dotenv::class)) {
			throw new \RuntimeException('Please required package symfony/dotenv.');
		}

		(new Symfony\Component\Dotenv\Dotenv(FALSE))->loadEnv($rootDir . '/.env');
	}
}

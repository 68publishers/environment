<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Bootstrap;

use Nette\Bootstrap\Configurator;
use Symfony\Component\Dotenv\Dotenv;
use SixtyEightPublishers\Environment\Helper\ProjectDirectoryResolver;
use SixtyEightPublishers\Environment\Debug\DebugModeDetectorInterface;

final class EnvBootstrap
{
	public const APP_ENV = 'APP_ENV';
	public const APP_DEBUG = 'APP_DEBUG';

	private function __construct()
	{
	}

	/**
	 * @param iterable<DebugModeDetectorInterface> $debugModeDetectors
	 *
	 * @return array<string, string>
	 */
	public static function boot(iterable $debugModeDetectors = [], ?string $rootDir = NULL): array
	{
		self::loadEnv($rootDir);
		self::detectDebugMode($debugModeDetectors);

		return $_ENV;
	}

	/**
	 * @param iterable<DebugModeDetectorInterface> $debugModeDetectors
	 *
	 * @return array<string, string>
	 */
	public static function bootNetteConfigurator(Configurator $configurator, iterable $debugModeDetectors = [], ?string $rootDir = NULL): array
	{
		$env = self::boot($debugModeDetectors, $rootDir);

		$configurator->setDebugMode((bool) $env[self::APP_DEBUG]);
		$configurator->addDynamicParameters([
			'env' => $env,
		]);

		return $env;
	}

	public static function loadEnv(?string $rootDir = NULL): void
	{
		$rootDir = self::resolveRootDir($rootDir);

		(new Dotenv())->bootEnv($rootDir . '/.env');
	}

	/**
	 * @param iterable<DebugModeDetectorInterface> $debugModeDetectors
	 */
	public static function detectDebugMode(iterable $debugModeDetectors = []): bool
	{
		$debug = FALSE;

		foreach ($debugModeDetectors as $debugModeDetector) {
			if ($debugModeDetector->detect()) {
				$debug = TRUE;

				break;
			}
		}

		$_SERVER[self::APP_DEBUG] = $_ENV[self::APP_DEBUG] = $debug ? '1' : '0';

		return $debug;
	}

	private static function resolveRootDir(?string $rootDir = NULL): string
	{
		return $rootDir ?? ProjectDirectoryResolver::resolveRootDir();
	}
}

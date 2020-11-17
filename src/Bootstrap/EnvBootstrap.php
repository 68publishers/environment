<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Bootstrap;

use ReflectionClass;
use RuntimeException;
use Nette\Configurator;
use ReflectionException;
use Composer\Autoload\ClassLoader;
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
	 * @param iterable|\SixtyEightPublishers\Environment\Debug\DebugModeDetectorInterface[] $debugModeDetectors
	 * @param string|NULL                                                                   $rootDir
	 *
	 * @return array
	 */
	public static function boot(iterable $debugModeDetectors = [], ?string $rootDir = NULL): array
	{
		self::loadEnv($rootDir);
		self::detectDebugMode($debugModeDetectors);

		return $_ENV;
	}

	/**
	 * @param \Nette\Configurator                                                           $configurator
	 * @param iterable|\SixtyEightPublishers\Environment\Debug\DebugModeDetectorInterface[] $debugModeDetectors
	 * @param string|NULL                                                                   $rootDir
	 *
	 * @return array
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

	/**
	 * @param string|NULL $rootDir
	 *
	 * @return void
	 * @throws \RuntimeException
	 */
	public static function loadEnv(?string $rootDir = NULL): void
	{
		$rootDir = self::resolveRootDir($rootDir);

		if (is_array($env = @include $rootDir . '/.env.local.php') && ($_SERVER[self::APP_ENV] ?? $_ENV[self::APP_ENV] ?? $env[self::APP_ENV]) === $env[self::APP_ENV]) {
			foreach ($env as $k => $v) {
				$_ENV[$k] = $_ENV[$k] ?? (isset($_SERVER[$k]) && 0 !== strpos($k, 'HTTP_') ? $_SERVER[$k] : $v);
			}

			return;
		}

		if (!class_exists(Dotenv::class)) {
			throw new RuntimeException('Please required package symfony/dotenv.');
		}

		(new Dotenv())->loadEnv($rootDir . '/.env');

		$_SERVER += $_ENV;
		$_SERVER[self::APP_ENV] = $_ENV[self::APP_ENV] = ($_SERVER[self::APP_ENV] ?? $_ENV[self::APP_ENV] ?? NULL) ?: 'dev';
	}

	/**
	 * @param iterable|\SixtyEightPublishers\Environment\Debug\DebugModeDetectorInterface[] $debugModeDetectors
	 *
	 * @return bool
	 */
	public static function detectDebugMode(iterable $debugModeDetectors = []): bool
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

	/**
	 * @param string|NULL $rootDir
	 *
	 * @return string
	 * @throws \RuntimeException
	 */
	private static function resolveRootDir(?string $rootDir = NULL): string
	{
		if (NULL !== $rootDir) {
			return $rootDir;
		}

		if (!class_exists(ClassLoader::class)) {
			throw new RuntimeException(sprintf(
				'Project root directory can\'t be detected because the class %s can\'t be found. Please provide the root directory manually.',
				ClassLoader::class
			));
		}

		try {
			$reflection = new ReflectionClass(ClassLoader::class);

			return dirname($reflection->getFileName(), 3);
		} catch (ReflectionException $e) {
			throw new RuntimeException('Project root directory can\'t be detected. Please provide the root directory manually.', 0, $e);
		}
	}
}

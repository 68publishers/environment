<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Tests\Bootstrap;

use Tester\Assert;
use Tester\Helpers;
use Tester\TestCase;
use Nette\Bootstrap\Configurator;
use SixtyEightPublishers\Environment\Debug\EnvDetector;
use SixtyEightPublishers\Environment\Bootstrap\EnvBootstrap;
use SixtyEightPublishers\Environment\Tests\Fixtures\ServiceFixture;
use function env;
use function assert;
use function uniqid;
use function sys_get_temp_dir;

require __DIR__ . '/../bootstrap.php';

final class EnvBootstrapTest extends TestCase
{
	public function testBootWithoutLocalPhpEnv(): void
	{
		$this->withEnvBackup(static function () {
			EnvBootstrap::boot([new EnvDetector()], __DIR__ . '/boot-without-local-php-env');

			Assert::same('test', env('APP_ENV'));
			Assert::same('1', env('APP_DEBUG'));
			Assert::same('a1', env('TEST_A'));
			Assert::same('b2', env('TEST_B'));
			Assert::same('15', env('TEST_C'));
		});
	}

	public function testBootWithoutLocalPhpEnvAndWithServerVariables(): void
	{
		$this->withEnvBackup(static function () {
			$_SERVER['APP_ENV'] = 'prod';
			$_SERVER['APP_DEBUG'] = '0';
			$_SERVER['TEST_A'] = 'a3';

			EnvBootstrap::boot([new EnvDetector()], __DIR__ . '/boot-without-local-php-env');

			Assert::same('prod', env('APP_ENV'));
			Assert::same('0', env('APP_DEBUG'));
			Assert::same('a3', env('TEST_A'));
			Assert::same('b1', env('TEST_B'));
			Assert::hasNotKey('TEST_C', $_ENV);
		});
	}

	public function testBootWithLocalPhpEnv(): void
	{
		$this->withEnvBackup(static function () {
			EnvBootstrap::boot([new EnvDetector()], __DIR__ . '/boot-with-local-php-env');

			Assert::same('prod', env('APP_ENV'));
			Assert::same('0', env('APP_DEBUG'));
			Assert::same('a1', env('TEST_A'));
			Assert::same('b1', env('TEST_B'));
			Assert::hasNotKey('TEST_C', $_ENV);
		});
	}

	public function testBootWithLocalPhpEnvAndWithServerVariables(): void
	{
		$this->withEnvBackup(static function () {
			$_SERVER['APP_ENV'] = 'staging';
			$_SERVER['APP_DEBUG'] = '1';
			$_SERVER['TEST_A'] = 'a3';

			EnvBootstrap::boot([new EnvDetector()], __DIR__ . '/boot-with-local-php-env');

			Assert::same('staging', env('APP_ENV'));
			Assert::same('1', env('APP_DEBUG'));
			Assert::same('a3', env('TEST_A'));
			Assert::same('b1', env('TEST_B'));
			Assert::hasNotKey('TEST_C', $_ENV);
		});
	}

	public function testBootNetteConfiguratorWithoutLocalPhpEnv(): void
	{
		$this->withEnvBackup(function () {
			$configurator = $this->createConfigurator();

			EnvBootstrap::bootNetteConfigurator($configurator, [new EnvDetector()], __DIR__ . '/boot-without-local-php-env');

			Assert::same('test', env('APP_ENV'));
			Assert::same('1', env('APP_DEBUG'));
			Assert::same('a1', env('TEST_A'));
			Assert::same('b2', env('TEST_B'));
			Assert::same('15', env('TEST_C'));

			Assert::true($configurator->isDebugMode());
			
			$container = $configurator->createContainer();
			$parameters = $container->getParameters();
			
			$serviceA = $container->getService('serviceA');
			assert($serviceA instanceof ServiceFixture);
			
			Assert::same('test', $serviceA->appEnv);
			Assert::true($serviceA->debugMode);
			Assert::same('a1', $serviceA->a);
			Assert::same('b2', $serviceA->b);
			Assert::same(15, $serviceA->c);

			Assert::same('test', $parameters['env']['APP_ENV']);
			Assert::same('1', $parameters['env']['APP_DEBUG']);
			Assert::same('a1', $parameters['env']['TEST_A']);
			Assert::same('b2', $parameters['env']['TEST_B']);
			Assert::same('15', $parameters['env']['TEST_C']);
		});
	}

	public function testBootNetteConfiguratorWithoutLocalPhpEnvAndWithServerVariables(): void
	{
		$this->withEnvBackup(function () {
			$_SERVER['APP_ENV'] = 'prod';
			$_SERVER['APP_DEBUG'] = '0';
			$_SERVER['TEST_A'] = 'a3';

			$configurator = $this->createConfigurator();

			EnvBootstrap::bootNetteConfigurator($configurator, [new EnvDetector()], __DIR__ . '/boot-without-local-php-env');

			Assert::same('prod', env('APP_ENV'));
			Assert::same('0', env('APP_DEBUG'));
			Assert::same('a3', env('TEST_A'));
			Assert::same('b1', env('TEST_B'));
			Assert::hasNotKey('TEST_C', $_ENV);

			Assert::false($configurator->isDebugMode());

			$container = $configurator->createContainer();
			$parameters = $container->getParameters();

			$serviceA = $container->getService('serviceA');
			assert($serviceA instanceof ServiceFixture);

			Assert::same('prod', $serviceA->appEnv);
			Assert::false($serviceA->debugMode);
			Assert::same('a3', $serviceA->a);
			Assert::same('b1', $serviceA->b);
			Assert::same(0, $serviceA->c);

			Assert::same('prod', $parameters['env']['APP_ENV']);
			Assert::same('0', $parameters['env']['APP_DEBUG']);
			Assert::same('a3', $parameters['env']['TEST_A']);
			Assert::same('b1', $parameters['env']['TEST_B']);
			Assert::hasNotKey('TEST_C', $parameters['env']);
		});
	}

	public function testBootNetteConfiguratorWithLocalPhpEnv(): void
	{
		$this->withEnvBackup(function () {
			$configurator = $this->createConfigurator();

			EnvBootstrap::bootNetteConfigurator($configurator, [new EnvDetector()], __DIR__ . '/boot-with-local-php-env');

			Assert::same('prod', env('APP_ENV'));
			Assert::same('0', env('APP_DEBUG'));
			Assert::same('a1', env('TEST_A'));
			Assert::same('b1', env('TEST_B'));
			Assert::hasNotKey('TEST_C', $_ENV);

			Assert::false($configurator->isDebugMode());

			$container = $configurator->createContainer();
			$parameters = $container->getParameters();

			$serviceA = $container->getService('serviceA');
			assert($serviceA instanceof ServiceFixture);

			Assert::same('prod', $serviceA->appEnv);
			Assert::false($serviceA->debugMode);
			Assert::same('a1', $serviceA->a);
			Assert::same('b1', $serviceA->b);
			Assert::same(0, $serviceA->c);

			Assert::same('prod', $parameters['env']['APP_ENV']);
			Assert::same('0', $parameters['env']['APP_DEBUG']);
			Assert::same('a1', $parameters['env']['TEST_A']);
			Assert::same('b1', $parameters['env']['TEST_B']);
			Assert::hasNotKey('TEST_C', $parameters['env']);
		});
	}

	public function testBootNetteConfiguratorWithLocalPhpEnvAndWithServerVariables(): void
	{
		$this->withEnvBackup(function () {
			$_SERVER['APP_ENV'] = 'staging';
			$_SERVER['APP_DEBUG'] = '1';
			$_SERVER['TEST_A'] = 'a3';

			$configurator = $this->createConfigurator();

			EnvBootstrap::bootNetteConfigurator($configurator, [new EnvDetector()], __DIR__ . '/boot-with-local-php-env');

			Assert::same('staging', env('APP_ENV'));
			Assert::same('1', env('APP_DEBUG'));
			Assert::same('a3', env('TEST_A'));
			Assert::same('b1', env('TEST_B'));
			Assert::hasNotKey('TEST_C', $_ENV);

			Assert::true($configurator->isDebugMode());

			$container = $configurator->createContainer();
			$parameters = $container->getParameters();

			$serviceA = $container->getService('serviceA');
			assert($serviceA instanceof ServiceFixture);

			Assert::same('staging', $serviceA->appEnv);
			Assert::true($serviceA->debugMode);
			Assert::same('a3', $serviceA->a);
			Assert::same('b1', $serviceA->b);
			Assert::same(0, $serviceA->c);

			Assert::same('staging', $parameters['env']['APP_ENV']);
			Assert::same('1', $parameters['env']['APP_DEBUG']);
			Assert::same('a3', $parameters['env']['TEST_A']);
			Assert::same('b1', $parameters['env']['TEST_B']);
			Assert::hasNotKey('TEST_C', $parameters['env']);
		});
	}

	public function createConfigurator(): Configurator
	{
		$tempDir = sys_get_temp_dir() . '/' . uniqid('68publishers:EnvBootstrapTest', TRUE);

		Helpers::purge($tempDir);

		$configurator = new Configurator();
		$configurator->setTempDirectory($tempDir);
		$configurator->addConfig(__DIR__ . '/config.neon');

		return $configurator;
	}
	
	private function withEnvBackup(callable $callback): void
	{
		$backup = [$_ENV, $_SERVER];

		try {
			$callback();
		} finally {
			[$_ENV, $_SERVER] = $backup;
		}
	}
}

(new EnvBootstrapTest())->run();

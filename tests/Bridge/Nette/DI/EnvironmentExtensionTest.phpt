<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Tests\Bootstrap;

use Tester\Assert;
use Tester\Helpers;
use Tester\TestCase;
use Nette\DI\Container;
use Nette\Bootstrap\Configurator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use SixtyEightPublishers\Environment\Debug\EnvDetector;
use SixtyEightPublishers\Environment\Bootstrap\EnvBootstrap;
use function assert;
use function uniqid;
use function file_exists;
use function sys_get_temp_dir;

require __DIR__ . '/../../../bootstrap.php';

final class EnvironmentExtensionTest extends TestCase
{
	public function testDotenvDumpCommand(): void
	{
		$tester = $this->createCommandTester('dotenv:dump');

		$tester->execute([]);

		$envLocalFile = __DIR__ . '/.env.local.php';

		Assert::true(file_exists($envLocalFile));

		$vars = require $envLocalFile;

		Assert::same([
			'APP_ENV' => 'dev',
			'APP_DEBUG' => '1',
			'TEST_A' => 'a',
			'TEST_B' => 'b',
			'TEST_C' => 'c',
		], $vars);

		unlink($envLocalFile);
	}

	public function testDebugCommand(): void
	{
		$tester = $this->createCommandTester('debug:dotenv');

		$tester->execute([]);

		Assert::same(file_get_contents(__DIR__ . '/expectedDebugOutput.txt'), $tester->getDisplay());
	}

	public function createCommandTester(string $name): CommandTester
	{
		$container = $this->createContainer();
		$application = $container->getByType(Application::class);
		assert($application instanceof Application);

		$command = $application->get($name);

		return new CommandTester($command);
	}

	public function createContainer(): Container
	{
		$tempDir = sys_get_temp_dir() . '/' . uniqid('68publishers:EnvironmentExtensionTest', TRUE);

		Helpers::purge($tempDir);

		$configurator = new Configurator();
		$configurator->setTempDirectory($tempDir);

		$configurator->addParameters([
			'cwd' => __DIR__,
		]);

		$configurator->addConfig(__DIR__ . '/config.neon');

		EnvBootstrap::bootNetteConfigurator($configurator, [new EnvDetector()], __DIR__);

		return $configurator->createContainer();
	}
}

(new EnvironmentExtensionTest())->run();

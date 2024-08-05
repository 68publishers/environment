<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Tests\Command;

use Closure;
use Tester\Assert;
use Composer\Config;
use Tester\TestCase;
use RuntimeException;
use Composer\IO\NullIO;
use Composer\Console\Application;
use Tester\CodeCoverage\Collector;
use Symfony\Component\Console\Tester\CommandTester;
use SixtyEightPublishers\Environment\Command\DumpEnvironmentCommand;

require __DIR__ . '/../bootstrap.php';

final class DumpEnvironmentCommandTest extends TestCase
{
	public function testFileIsCreated(): void
	{
		$this->runDumpEnvironmentCommand(
			__DIR__ . '/file-is-created',
			[
				'env' => 'prod',
			],
			[
				'APP_ENV' => 'prod',
				'APP_SECRET' => 'abcdefgh123456789',
			]
		);
	}

	public function testEmptyOptionMustIgnoreContent(): void
	{
		$this->runDumpEnvironmentCommand(
			__DIR__ . '/empty-option-must-ignore-content',
			[
				'env' => 'prod',
				'--empty' => TRUE,
			],
			[
				'APP_ENV' => 'prod',
			]
		);
	}

	public function testEnvCanBeReferenced(): void
	{
		$backup = $_SERVER;

		$_SERVER['FOO'] = 'Foo';
		$_SERVER['BAR'] = 'Bar';

		try {
			$this->runDumpEnvironmentCommand(
				__DIR__ . '/env-can-be-referenced',
				[
					'env' => 'prod',
				],
				[
					'APP_ENV' => 'prod',
					'BAR' => 'Foo',
					'FOO' => '123',
				]
			);
		} finally {
			$_SERVER = $backup;
		}
	}

	public function testRequiresToSpecifyEnvArgumentWhenLocalFileDoesNotSpecifyAppEnv(): void
	{
		Assert::exception(function () {
			$this->runDumpEnvironmentCommand(
				__DIR__ . '/requires-to-specify-env-argument-when-local-file-does-not-specify-app-env',
				[],
				[]
			);
		}, RuntimeException::class, 'Please provide the name of the environment either by passing it as command line argument or by defining the "APP_ENV" variable in the ".env.local" file.');
	}

	public function testDoesNotRequireToSpecifyEnvArgumentWhenLocalFileIsPresent(): void
	{
		$this->runDumpEnvironmentCommand(
			__DIR__ . '/does-not-require-to-specify-env-argument-when-local-file-is-present',
			[],
			[
				'APP_ENV' => 'staging',
			]
		);
	}

	protected function tearDown(): void
	{
		# save manually partial code coverage to free memory
		if (Collector::isStarted()) {
			Collector::save();
		}
	}

	private function runDumpEnvironmentCommand(string $directory, array $args, array $expected): void
	{
		$envLocalFile = $directory . '/.env.local.php';

		@unlink($envLocalFile);

		$this->createDumpEnvironmentCommandTester($directory)->execute($args);

		Assert::true(file_exists($envLocalFile));

		$vars = require $envLocalFile;

		foreach ($expected as $envKey => $envVar) {
			Assert::hasKey($envKey, $vars);
			Assert::same($envVar, $vars[$envKey]);
		}

		unlink($envLocalFile);
	}

	private function createDumpEnvironmentCommandTester(string $directory): CommandTester
	{
		$command = new DumpEnvironmentCommand(
			new Config(FALSE, __DIR__ . '/../..'),
			$directory
		);

		$application = new Application();

		Closure::bind(function () {
			$this->io = new NullIO();
		}, $application, $application)();

		$application->add($command);
		$command = $application->find('dotenv:dump');

		return new CommandTester($command);
	}
}

(new DumpEnvironmentCommandTest())->run();

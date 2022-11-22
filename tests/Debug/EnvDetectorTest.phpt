<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Tests\Debug;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\Environment\Debug\EnvDetector;
use SixtyEightPublishers\Environment\Bootstrap\EnvBootstrap;

require __DIR__ . '/../bootstrap.php';

final class EnvDetectorTest extends TestCase
{
	private EnvDetector $detector;

	protected function setUp(): void
	{
		$this->detector = new EnvDetector();
	}

	public function testDebugModeShouldBeDetectedWithoutEnvVariables(): void
	{
		Assert::true($this->detector->detect());
	}

	public function testDebugModeShouldBeDetectedIfDebugVariableEqualsToTrue(): void
	{
		$_SERVER[EnvBootstrap::APP_DEBUG] = '1';
		Assert::true($this->detector->detect());
		unset($_SERVER[EnvBootstrap::APP_DEBUG]);

		$_ENV[EnvBootstrap::APP_DEBUG] = '1';
		Assert::true($this->detector->detect());
		unset($_ENV[EnvBootstrap::APP_DEBUG]);
	}

	public function testDebugModeShouldBeDetectedIfAppEnvVariableIsNotSetToProduction(): void
	{
		$_SERVER[EnvBootstrap::APP_ENV] = 'dev';
		Assert::true($this->detector->detect());

		$_SERVER[EnvBootstrap::APP_ENV] = 'test';
		Assert::true($this->detector->detect());

		$_SERVER[EnvBootstrap::APP_ENV] = 'local';
		Assert::true($this->detector->detect());

		unset($_SERVER[EnvBootstrap::APP_ENV]);
	}

	public function testDebugModeShouldNotBeDetectedIfDebugVariableEqualsToFalse(): void
	{
		$_SERVER[EnvBootstrap::APP_DEBUG] = '0';
		Assert::false($this->detector->detect());
		unset($_SERVER[EnvBootstrap::APP_DEBUG]);

		$_ENV[EnvBootstrap::APP_DEBUG] = '0';
		Assert::false($this->detector->detect());
		unset($_ENV[EnvBootstrap::APP_DEBUG]);
	}

	public function testDebugModeShouldNotBeDetectedIfAppEnvVariableIsSetToProduction(): void
	{
		$_SERVER[EnvBootstrap::APP_ENV] = 'prod';
		Assert::false($this->detector->detect());
		unset($_SERVER[EnvBootstrap::APP_ENV]);
	}
}

(new EnvDetectorTest())->run();

<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Tests\Debug;

use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

final class CliOptionDetectorTest extends TestCase
{
	public function testDebugModeShouldNotBeDetectedOnPhpScriptWithoutOption(): void
	{
		Assert::same('no', shell_exec('php ./cliOptionDetectorScript.php'));
	}

	public function testDebugModeShouldBeDetectedOnPhpScriptWithOption(): void
	{
		Assert::same('yes', shell_exec('php ./cliOptionDetectorScript.php --app-debug'));
	}
}

(new CliOptionDetectorTest())->run();

<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Tests\Debug;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\Environment\Debug\IpAddressDetector;

require __DIR__ . '/../bootstrap.php';

final class IpAddressDetectorTest extends TestCase
{
	public function testDebugModeDetectionWithoutCookieCheck(): void
	{
		$detector = new IpAddressDetector([
			'127.0.0.1',
			'secretValue@127.0.0.2',
		], NULL);

		Assert::false($detector->detect());

		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
		Assert::true($detector->detect());

		$_SERVER['REMOTE_ADDR'] = '127.0.0.2';
		Assert::false($detector->detect());

		unset($_SERVER['REMOTE_ADDR']);
	}

	public function testDebugModeDetectionWithCookieCheck(): void
	{
		$detector = new IpAddressDetector([
			'127.0.0.1',
			'secretValue@127.0.0.2',
		]);

		Assert::false($detector->detect());

		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
		Assert::true($detector->detect());

		$_SERVER['REMOTE_ADDR'] = '127.0.0.2';
		Assert::false($detector->detect());
		$_COOKIE['app-debug'] = 'invalidSecretValue';
		Assert::false($detector->detect());
		$_COOKIE['app-debug'] = 'secretValue';
		Assert::true($detector->detect());

		unset($_SERVER['REMOTE_ADDR'], $_COOKIE['app-debug']);
	}
}

(new IpAddressDetectorTest())->run();

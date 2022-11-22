<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Tests\Debug;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\Environment\Debug\SimpleCookieDetector;

require __DIR__ . '/../bootstrap.php';

final class SimpleCookieDetectorTest extends TestCase
{
	private SimpleCookieDetector $detector;

	protected function setUp(): void
	{
		$this->detector = new SimpleCookieDetector('allow');
	}

	public function testDebugModeShouldNotBeDetectedIfCookieNotExists(): void
	{
		if (!isset($_COOKIE['app-debug'])) {
			unset($_COOKIE['app-debug']);
		}

		Assert::false($this->detector->detect());
	}

	public function testDebugModeShouldNotBeDetectedIfCookieExistsWithIncorrectValue(): void
	{
		$_COOKIE['app-debug'] = 'deny';

		Assert::false($this->detector->detect());
	}

	public function testDebugModeShouldBeDetectedIfCookieExistsWithCorrectValue(): void
	{
		$_COOKIE['app-debug'] = 'allow';

		Assert::true($this->detector->detect());
	}
}

(new SimpleCookieDetectorTest())->run();

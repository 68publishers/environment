<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Tests\Debug;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\Environment\Debug\SecretCookieDetector;
use function str_repeat;

require __DIR__ . '/../bootstrap.php';

final class SecretCookieDetectorTest extends TestCase
{
	private const COOKIE = 'debug_please';

	/**
	 * @return array<string, array{0: string|NULL, 1: string|array<string>|NULL, 2: bool}>
	 */
	public function getRequests(): array
	{
		# the boundary is written out rather than read from the detector, so lowering the default fails here
		$short = str_repeat('a', 31);
		$strong = str_repeat('a', 32);
		$requests = [];

		foreach (['unset' => NULL, 'empty' => '', 'a placeholder' => 'value', 'one character short' => $short, 'of 16 two-byte characters' => str_repeat('é', 16)] as $label => $secret) {
			$requests['secret ' . $label . ', no cookie'] = [$secret, NULL, FALSE];
			$requests['secret ' . $label . ', empty cookie'] = [$secret, '', FALSE];
			$requests['secret ' . $label . ', wrong cookie'] = [$secret, 'wrong', FALSE];
			$requests['secret ' . $label . ', the secret as the cookie'] = [$secret, $secret ?? '', FALSE];
		}

		$requests['strong secret, no cookie'] = [$strong, NULL, FALSE];
		$requests['strong secret, empty cookie'] = [$strong, '', FALSE];
		$requests['strong secret, wrong cookie'] = [$strong, str_repeat('b', 32), FALSE];
		$requests['strong secret, a prefix of it as the cookie'] = [$strong, $short, FALSE];
		$requests['strong secret, an array cookie'] = [$strong, [$strong], FALSE];
		$requests['strong secret, the secret as the cookie'] = [$strong, $strong, TRUE];

		return $requests;
	}

	/**
	 * @dataProvider getRequests
	 *
	 * @param string|array<string>|NULL $cookie
	 */
	public function testDebugModeIsDetectedOnlyForStrongSecretSentBack(?string $secret, string|array|NULL $cookie, bool $expected): void
	{
		$this->withCookie($cookie, function () use ($secret, $expected): void {
			Assert::same($expected, (new SecretCookieDetector($secret, self::COOKIE))->detect());
		});
	}

	public function testMinimumSecretLengthCanBeChanged(): void
	{
		$secret = str_repeat('a', 16);

		$this->withCookie($secret, function () use ($secret): void {
			Assert::false((new SecretCookieDetector($secret, self::COOKIE))->detect());
			Assert::true((new SecretCookieDetector($secret, self::COOKIE, 16))->detect());
			Assert::false((new SecretCookieDetector($secret, self::COOKIE, 17))->detect());
		});
	}

	public function testEmptySecretIsRejectedEvenWithoutMinimumLength(): void
	{
		$this->withCookie('', function (): void {
			Assert::false((new SecretCookieDetector('', self::COOKIE, 0))->detect());
		});
	}

	public function testDefaultCookieName(): void
	{
		$secret = str_repeat('a', 32);
		$_COOKIE['app-debug'] = $secret;

		try {
			Assert::true((new SecretCookieDetector($secret))->detect());
		} finally {
			unset($_COOKIE['app-debug']);
		}
	}

	/**
	 * @param string|array<string>|NULL $cookie
	 */
	private function withCookie(string|array|NULL $cookie, callable $callback): void
	{
		unset($_COOKIE[self::COOKIE]);

		if (NULL !== $cookie) {
			$_COOKIE[self::COOKIE] = $cookie;
		}

		try {
			$callback();
		} finally {
			unset($_COOKIE[self::COOKIE]);
		}
	}
}

(new SecretCookieDetectorTest())->run();

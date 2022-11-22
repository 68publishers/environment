<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Tests\Fixtures;

final class ServiceFixture
{
	public string $appEnv;

	public bool $debugMode;

	public string $a;

	public string $b;

	public ?int $c;

	public function __construct(string $appEnv, bool $debugMode, string $a, string $b, ?int $c)
	{
		$this->appEnv = $appEnv;
		$this->debugMode = $debugMode;
		$this->a = $a;
		$this->b = $b;
		$this->c = $c;
	}
}

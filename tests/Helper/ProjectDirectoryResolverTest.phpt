<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Tests\Helper;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\Environment\Helper\ProjectDirectoryResolver;
use function realpath;

require __DIR__ . '/../bootstrap.php';

final class ProjectDirectoryResolverTest extends TestCase
{
	public function testResolveRootDir(): void
	{
		Assert::same(realpath(__DIR__ . '/../..'), ProjectDirectoryResolver::resolveRootDir());
	}
}

(new ProjectDirectoryResolverTest())->run();

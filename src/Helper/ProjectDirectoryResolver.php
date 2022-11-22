<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Helper;

use ReflectionClass;
use RuntimeException;
use ReflectionException;
use Composer\Autoload\ClassLoader;
use function dirname;
use function class_exists;

final class ProjectDirectoryResolver
{
	private function __construct()
	{
	}

	public static function resolveRootDir(): string
	{
		if (!class_exists(ClassLoader::class)) {
			throw new RuntimeException(sprintf(
				'Project root directory can\'t be detected because the class %s can\'t be found. Please provide the root directory manually.',
				ClassLoader::class
			));
		}

		try {
			$reflection = new ReflectionClass(ClassLoader::class);
			$filename = $reflection->getFileName();

			if (FALSE === $filename) {
				throw new RuntimeException('Project root directory can\'t be detected. Please provide the root directory manually.', 0);
			}

			return dirname($filename, 3);
		} catch (ReflectionException $e) {
			throw new RuntimeException('Project root directory can\'t be detected. Please provide the root directory manually.', 0, $e);
		}
	}
}

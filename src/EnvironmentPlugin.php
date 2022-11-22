<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\Capability\CommandProvider as CommandProviderInterface;

final class EnvironmentPlugin implements PluginInterface, Capable
{
	public function activate(Composer $composer, IOInterface $io): void
	{
	}

	public function deactivate(Composer $composer, IOInterface $io): void
	{
	}

	public function uninstall(Composer $composer, IOInterface $io): void
	{
	}

	public function getCapabilities(): array
	{
		return [
			CommandProviderInterface::class => CommandProvider::class,
		];
	}
}

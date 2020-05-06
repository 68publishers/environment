<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment;

use Composer;

final class EnvironmentPlugin implements Composer\Plugin\PluginInterface, Composer\Plugin\Capable
{
	/**************** interface Composer\Plugin\PluginInterface ****************[

	/**
	 * {@inheritDoc}
	 */
	public function activate(Composer\Composer $composer, Composer\IO\IOInterface $io): void
	{
	}

	/**************** interface Composer\Plugin\Capable ****************[

	/**
	 * {@inheritDoc}
	 */
	public function getCapabilities(): array
	{
		return [
			Composer\Plugin\Capability\CommandProvider::class => CommandProvider::class,
		];
	}
}

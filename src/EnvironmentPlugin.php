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
	/**************** interface Composer\Plugin\PluginInterface ****************[

	/**
	 * {@inheritDoc}
	 */
	public function activate(Composer $composer, IOInterface $io): void
	{
	}

	/**
	 * {@inheritDoc}
	 */
	public function deactivate(Composer $composer, IOInterface $io)
	{
	}

	/**
	 * {@inheritDoc}
	 */
	public function uninstall(Composer $composer, IOInterface $io)
	{
	}

	/**************** interface Composer\Plugin\Capable ****************[

	/**
	 * {@inheritDoc}
	 */
	public function getCapabilities(): array
	{
		return [
			CommandProviderInterface::class => CommandProvider::class,
		];
	}
}

<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment;

use Composer\Plugin\Capability\CommandProvider as CommandProviderInterface;

final class CommandProvider implements CommandProviderInterface
{
	/** @var \Composer\Composer  */
	private $composer;

	/**
	 * @param array $args
	 */
	public function __construct(array $args)
	{
		$this->composer = $args['composer'];
	}

	/**************** interface Composer\Plugin\Capability\CommandProvider ****************[

	/**
	 * {@inheritDoc}
	 */
	public function getCommands(): array
	{
		return [
			new Command\DumpEnvironmentCommand(dirname($this->composer->getConfig()->get('vendor-dir'))),
		];
	}
}

<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment;

use Composer\Composer;
use SixtyEightPublishers\Environment\Command\DumpEnvironmentCommand;
use Composer\Plugin\Capability\CommandProvider as CommandProviderInterface;

final class CommandProvider implements CommandProviderInterface
{
	private Composer $composer;

	/**
	 * @param array{composer: Composer} $args
	 */
	public function __construct(array $args)
	{
		$this->composer = $args['composer'];
	}

	public function getCommands(): array
	{
		return [
			new DumpEnvironmentCommand($this->composer->getConfig()),
		];
	}
}

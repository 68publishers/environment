<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Bridge\Nette\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Symfony\Component\Dotenv\Command\DebugCommand;
use Symfony\Component\Dotenv\Command\DotenvDumpCommand;
use SixtyEightPublishers\Environment\Helper\ProjectDirectoryResolver;

final class EnvironmentExtension extends CompilerExtension
{
	private string $projectDir;

	public function __construct(?string $projectDir = NULL)
	{
		$this->projectDir = $projectDir ?? ProjectDirectoryResolver::resolveRootDir();
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('command.dump'))
			->setType(DotenvDumpCommand::class)
			->setArguments([
				'projectDir' => $this->projectDir,
				'defaultEnv' => new Statement('::env', ['APP_ENV']),
			]);

		$builder->addDefinition($this->prefix('command.debug'))
			->setType(DebugCommand::class)
			->setArguments([
				// positional: the second parameter is `$projectDirectory` in symfony/dotenv < 7.4.6 / 8.0.0 - 8.0.5 and `$projectDir` since 7.4.6 / 8.0.6
				new Statement('::env', ['APP_ENV']),
				$this->projectDir,
			]);
	}
}

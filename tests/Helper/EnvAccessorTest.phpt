<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Tests\Helper;

use Tester\Assert;
use Tester\TestCase;
use InvalidArgumentException;
use SixtyEightPublishers\Environment\Helper\EnvAccessor;

require __DIR__ . '/../bootstrap.php';

final class EnvAccessorTest extends TestCase
{
	public function testEnvVariablesShouldBeReturned(): void
	{
		$this->withEnvBackup(static function () {
			$_ENV['TEST_A'] = '123';
			$_ENV['TEST_B'] = '0';

			Assert::same('123', EnvAccessor::getEnv('TEST_A'));
			Assert::same('123', EnvAccessor::getEnv('TEST_A', [], '456'));
			Assert::same('0', EnvAccessor::getEnv('TEST_B'));
			Assert::same('0', EnvAccessor::getEnv('TEST_B', [], '1'));
		});
	}

	public function testDefaultValueShouldBeReturnedIfEnvVariableIsNotDefined(): void
	{
		Assert::same('123', EnvAccessor::getEnv('TEST', [], '123'));
		Assert::same(123, EnvAccessor::getEnv('TEST', ['integer'], '123'));
		Assert::null(EnvAccessor::getEnv('TEST', [], NULL));
	}

	public function testExceptionShouldBeThrownIfEnvVariableIsNotDefined(): void
	{
		Assert::exception(
			static fn () => EnvAccessor::getEnv('TEST'),
			InvalidArgumentException::class,
			'ENV variable TEST is not defined.'
		);
	}

	public function testFilters(): void
	{
		$this->withEnvBackup(static function () {
			$_ENV['TEST_STRING'] = 'abc';
			$_ENV['TEST_STRING_WITH_EXTRA_SPACES'] = '  efd  ';
			$_ENV['TEST_INTEGER'] = '123';
			$_ENV['TEST_FLOAT'] = '123.56';
			$_ENV['TEST_BOOLEAN'] = '1';
			$_ENV['TEST_JSON'] = '{"foo":"abc","bar":15}';
			$_ENV['TEST_EMPTY'] = '';
			$_ENV['TEST_SPACE_ONLY'] = ' ';

			# type casting
			Assert::same('abc', EnvAccessor::getEnv('TEST_STRING', ['string']));
			Assert::same(123, EnvAccessor::getEnv('TEST_INTEGER', ['integer']));
			Assert::same(123, EnvAccessor::getEnv('TEST_INTEGER', ['int']));
			Assert::same(123.56, EnvAccessor::getEnv('TEST_FLOAT', ['float']));
			Assert::true(EnvAccessor::getEnv('TEST_BOOLEAN', ['boolean']));
			Assert::true(EnvAccessor::getEnv('TEST_BOOLEAN', ['bool']));

			# converting
			Assert::same(base64_encode('abc'), EnvAccessor::getEnv('TEST_STRING', ['base64']));
			Assert::same(['foo' => 'abc', 'bar' => 15], EnvAccessor::getEnv('TEST_JSON', ['json_decode']));

			# negation
			Assert::false(EnvAccessor::getEnv('TEST_BOOLEAN', ['negate']));
			Assert::false(EnvAccessor::getEnv('TEST_BOOLEAN', ['not']));

			# nullable
			Assert::same('abc', EnvAccessor::getEnv('TEST_STRING', ['nullable']));
			Assert::null(EnvAccessor::getEnv('TEST_EMPTY', ['nullable']));
			Assert::same(' ', EnvAccessor::getEnv('TEST_SPACE_ONLY', ['nullable']));

			# trim
			Assert::same('efd', EnvAccessor::getEnv('TEST_STRING_WITH_EXTRA_SPACES', ['trim']));

			# chained
			Assert::null(EnvAccessor::getEnv('TEST_SPACE_ONLY', ['trim', 'nullable']));
			Assert::same(\base64_encode('efd'), EnvAccessor::getEnv('TEST_STRING_WITH_EXTRA_SPACES', ['trim', 'base64']));
		});
	}

	private function withEnvBackup(callable $callback): void
	{
		$backup = [$_ENV, $_SERVER];

		try {
			$callback();
		} finally {
			[$_ENV, $_SERVER] = $backup;
		}
	}
}

(new EnvAccessorTest())->run();

<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Helper;

use JsonException;
use InvalidArgumentException;
use function trim;
use function sprintf;
use function is_string;
use function json_decode;
use function func_num_args;
use function array_key_exists;

final class EnvAccessor
{
	/** @var array<string, callable>  */
	public static array $filters = [
		'string' => 'strval',
		'integer' => 'intval',
		'int' => 'intval',
		'float' => 'floatval',
		'boolean' => 'boolval',
		'bool' => 'boolval',
		'base64'=> 'base64_encode',
		'json_decode' => [__CLASS__, 'toJsonArray'],
		'not' => [__CLASS__, 'negate'],
		'negate' => [__CLASS__, 'negate'],
		'nullable' => [__CLASS__, 'nullable'],
		'trim' => [__CLASS__, 'trim'],
	];

	private function __construct()
	{
	}

	/**
	 * @param array<string> $filters
	 * @param mixed|null    $default
	 *
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	public static function getEnv(string $name, array $filters = [], $default = NULL)
	{
		if (array_key_exists($name, $_ENV)) {
			$value = $_ENV[$name];
		} else {
			if (3 > func_num_args()) {
				throw new InvalidArgumentException(sprintf(
					'ENV variable %s is not defined.',
					$name
				));
			}

			$value = $default;
		}

		foreach ($filters as $filter) {
			$value = self::$filters[$filter]($value);
		}

		return $value;
	}

	/**
	 * @interal
	 * @param mixed $value
	 *
	 * @return mixed
	 * @throws JsonException
	 */
	public static function toJsonArray($value)
	{
		return is_string($value) ? json_decode($value, TRUE, 512, JSON_THROW_ON_ERROR) : $value;
	}

	/**
	 * @interal
	 * @param mixed $value
	 */
	public static function negate($value): bool
	{
		return !$value;
	}

	/**
	 * @interal
	 * @param mixed $value
	 *
	 * @return mixed|NULL
	 */
	public static function nullable($value)
	{
		return '' === $value ? NULL : $value;
	}

	/**
	 * @interal
	 * @param mixed $value
	 *
	 * @return mixed|string
	 */
	public static function trim($value)
	{
		return is_string($value) ? trim($value) : $value;
	}
}

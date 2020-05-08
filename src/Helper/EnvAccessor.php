<?php

declare(strict_types=1);

namespace SixtyEightPublishers\Environment\Helper;

final class EnvAccessor
{
	/** @var array  */
	public static $filters = [
		'string' => 'strval',
		'integer' => 'intval',
		'int' => 'intval',
		'float' => 'floatval',
		'boolean' => 'boolval',
		'bool' => 'boolval',
		'base64'=> 'base64_encode',
		'json_encode' => [__CLASS__, 'toJsonArray'],
	];

	/**
	 * @throws \RuntimeException
	 */
	public function __construct()
	{
		throw new \RuntimeException(sprintf(
			'Class %s can\'t be initialized via the constructor.',
			static::class
		));
	}

	/**
	 * @param string     $name
	 * @param array      $filters
	 * @param mixed|NULL $default
	 *
	 * @return mixed
	 */
	public static function getEnv(string $name, array $filters = [], $default = NULL)
	{
		if (array_key_exists($name, $_ENV)) {
			$value = $_ENV[$name];
		} else {
			if (3 > func_num_args()) {
				throw new \InvalidArgumentException(sprintf(
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
	 * @internal
	 *
	 * @param mixed $value
	 *
	 * @return array
	 */
	public static function toJsonArray($value): array
	{
		return json_decode($value, TRUE);
	}
}

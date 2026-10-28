# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0] - 2026-10-28

### Added
- Support for `symfony/dotenv` `^8.0` (and `symfony/console` `^8.0` for the commands).

### Changed
- Minimal PHP version is 8.3.
- The `debug:dotenv` command service (`EnvironmentExtension`) gets its constructor arguments positionally.
- Dev tooling: PHPStan 2, Nette Tester 2.6+, PHP-CS-Fixer 3.89+, CI matrix PHP 8.3, 8.4 and 8.5 with the lowest and highest dependencies.

### Removed
- ❗ Support for PHP < 8.3.

### Fixed
- `EnvironmentExtension` failed to compile the container with `symfony/dotenv` >= 7.4.6 / 8.0.6 because `DebugCommand::__construct()` renamed its argument `$projectDirectory` to `$projectDir`.
- The `base64` filter of `EnvAccessor` (and `env()`) threw a `TypeError` for non-string values (e.g. `NULL` from the `nullable` filter or a default value). Non-string values are now returned unchanged, the same as with the `trim` filter.

## [1.1.0] - 2024-08-06

### Added
- Support for `symfony/dotenv` `^7.0`.

## [1.0.1] - 2022-11-29

### Changed
- The argument `$projectDir` of `EnvironmentExtension::__construct()` has the default value `null`.
- `Makefile` is excluded from the export.

### Removed
- `.travis.yml`.

## [1.0.0] - 2022-11-22

### Added
- Support for PHP 7.4 – 8.2.
- Nette compiler extension `EnvironmentExtension` (not necessary for variables loading, see the README).
- Filters `nullable` and `trim` in `EnvAccessor`.
- Dockerfile, docker-compose.yml, Makefile, tests, PHPStan and GitHub Actions.

### Changed
- ❗ Renamed the command `environment:dump`/`env:dump` to `dotenv:dump`/`dump-env`.
- Updated the `symfony/dotenv` dependency to `^5.4 || ^6.0`.

### Removed
- Support for PHP < 7.4.

## [0.5.0] - 2022-08-07

### Added
- PHP 8 support.

## [0.4] - 2021-10-07

### Fixed
- Renamed the env filter `json_encode` to `json_decode`.

## [0.3] - 2020-11-19

### Added
- Composer 2 support (version 1 is still supported).

## [0.2] - 2020-11-17

### Changed
- `EnvBootstrap` improvements.
- Coding style and dependencies.

### Fixed
- Removed the boolean argument from initializations of the `Dotenv` class (the constructor arguments were changed in `symfony/dotenv` 5.1).

## [0.1.4] - 2020-06-01

### Fixed
- The function `env()` wasn't autoloaded in applications.

## [0.1.3] - 2020-05-21

### Changed
- `EnvBootstrap` separates the ENV loading and the debug mode detection.
- Renamed the default cookie name and the CLI option name to `app-debug`.

## [0.1.2] - 2020-05-15

### Fixed
- Markup of the PHP snippets in the README.

## [0.1.1] - 2020-05-15

### Added
- Function `env()`.

## [0.1] - 2020-05-07

- First release.

[Unreleased]: https://github.com/68publishers/environment/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/68publishers/environment/compare/v1.1.0...v2.0.0
[1.1.0]: https://github.com/68publishers/environment/compare/v1.0.1...v1.1.0
[1.0.1]: https://github.com/68publishers/environment/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/68publishers/environment/compare/v0.5.0...v1.0.0
[0.5.0]: https://github.com/68publishers/environment/compare/v0.4...v0.5.0
[0.4]: https://github.com/68publishers/environment/compare/v0.3...v0.4
[0.3]: https://github.com/68publishers/environment/compare/v0.2...v0.3
[0.2]: https://github.com/68publishers/environment/compare/v0.1.4...v0.2
[0.1.4]: https://github.com/68publishers/environment/compare/v0.1.3...v0.1.4
[0.1.3]: https://github.com/68publishers/environment/compare/v0.1.2...v0.1.3
[0.1.2]: https://github.com/68publishers/environment/compare/v0.1.1...v0.1.2
[0.1.1]: https://github.com/68publishers/environment/compare/v0.1...v0.1.1
[0.1]: https://github.com/68publishers/environment/releases/tag/v0.1

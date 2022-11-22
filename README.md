<h1 align="center">Environment</h1>

<p align="center">The component based on the <a href="https://symfony.com/doc/current/components/dotenv.html">symfony/dotenv</a> for simple loading of the ENV variables and detecting a debug mode.</p>

<p align="center">
<a href="https://github.com/68publishers/environment/actions"><img alt="Checks" src="https://badgen.net/github/checks/68publishers/environment/master"></a>
<a href="https://coveralls.io/github/68publishers/environment?branch=master"><img alt="Coverage Status" src="https://coveralls.io/repos/github/68publishers/environment/badge.svg?branch=master"></a>
<a href="https://packagist.org/packages/68publishers/environment"><img alt="Total Downloads" src="https://badgen.net/packagist/dt/68publishers/environment"></a>
<a href="https://packagist.org/packages/68publishers/environment"><img alt="Latest Version" src="https://badgen.net/packagist/v/68publishers/environment"></a>
<a href="https://packagist.org/packages/68publishers/environment"><img alt="PHP Version" src="https://badgen.net/packagist/php/68publishers/environment"></a>
</p>

## Installation

```sh
$ composer require 68publishers/environment
```

## Usage

### Default ENV variables

The default ENV variables are:

- APP_ENV
- APP_DEBUG 

Both variables will be always accessible in the global arrays `$_ENV` and `$_SERVER`. 
The default value for `APP_ENV` is `dev` and `0` for `APP_DEBUG`.

### ENV variables loading  

For ENV variables loading call this static method after requiring Composer's autoload and before your application is stared. 
The first argument is a relative path to the application root directory and the second argument is an array of debug mode detectors.

```php
<?php

use SixtyEightPublishers\Environment\Debug;
use SixtyEightPublishers\Environment\Bootstrap\EnvBootstrap;

require __DIR__ . '/../vendor/autoload.php';

EnvBootstrap::boot([
    new Debug\CliOptionDetector('debug_please'), // the debug mode is enabled if an option "--debug_please" is defined (CLI only)
    new Debug\IpAddressDetector([
        '185.141.252.240', // the debug mode is enabled always for this IP address
        'john_dee@135.151.252.240', // the debug mode is enabled for this IP address and a cookie called "debug_please" must exist with value "john_dee"
    ], 'debug_please'),
    new Debug\SimpleCookieDetector('ineeddebug123', 'debug_please'), // the debug mode is enabled if a cookie called "debug_please" exists and has the value "ineeddebug123"
    new Debug\EnvDetector(), // the detection is performed from loaded ENV variables, the debug mode is enabled if a variable "APP_DEBUG=1" is defined or if a variable "APP_ENV" has a different value than, "prod"
]);

// All your ENV variables are now accessible in the global arrays `$_ENV` and `$_SERVER`
```

If you're using [Nette Framework](https://nette.org) then you can use this method in the application bootstrap instead:

```php
<?php

use Nette\Bootstrap\Configurator;
use SixtyEightPublishers\Environment\Bootstrap\EnvBootstrap;

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Configurator();

EnvBootstrap::bootNetteConfigurator($configurator, [
	// define detectors here
]);

// All your ENV variables are now accessible in the global arrays `$_ENV` and `$_SERVER`
// The debug mode on the configurator is set by the ENV variable `APP_DEBUG`
// The ENV variables are accessible in DI container and Neon configuration as dynamic parameters with prefix `env.` e.g. `%env.APP_ENV%`
```

### ENV variables caching/dumping

All ENV variables are always (in each request) parsed from `.env` files by default. 
This is a good solution for developers because all changes are immediately applied after the change.
But sometimes (mainly on the production) you don't want to parse `.env` files in each request. If you want to cache the ENV variables then run following Composer command:

```bash
$ composer dotenv:dump <APP_ENV>

or shorter

$ composer dump-env <APP_ENV>
```

The file `.env.local.php` will be created in the application's root directory, and it will be used instead of all your `.env` files.

### Nette DI Extension

The package includes the Compiler Extension to Nette DI. Its registration is not necessary for variables loading to work, but it adds two console commands to the application.

```neon
extensions:
	environment: SixtyEightPublishers\Environment\Bridge\Nette\DI\EnvironmentExtension
```

#### Command `dotenv:dump`

The command works just like the composer command. The `env` argument is optional here, the current `APP_ENV` of the application is used as the default value.

```sh
$ bin/console dotenv:dump [<env>] [--empty]
```

#### Command `debug:dotenv`

The command lists all dotenv files with variables and values.

```sh
$ bin/console debug:dotenv
```

## Contributing

Before opening a pull request, please check your changes using the following commands

```bash
$ make init # to pull and start all docker images

$ make cs.check
$ make stan
$ make tests.all
```

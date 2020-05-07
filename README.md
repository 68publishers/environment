# Environment

The component based on the [symfony/dotenv](https://symfony.com/doc/current/components/dotenv.html) for simple loading of the ENV variables and detecting a debug mode.

## Installation

```bash
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
use SixtyEightPublishers\Environment\Debug;
use SixtyEightPublishers\Environment\Bootstrap\EnvBootstrap;

require __DIR__ . '/../vendor/autoload.php';

EnvBootstrap::boot(__DIR__ . '/..', [
    new Debug\CliOptionDetector('debug_please'), // the debug mode is enabled if an option "--debug_please" is defined (CLI only)
    new Debug\IpAddressDetector([
        '185.141.252.240', // the debug mode is enabled always for this IP address
        'john_dee@135.151.252.240', // the debug mode is enabled for this IP address and a cookie called "debug_please" must exist with value "john_dee"
    ], 'debug_please'),
    new Debug\SimpleCookieDetector('ineeddebug123', 'debug_please'), // the debug mode is enabled if a cookie called "debug_please" exists and has the value "ineeddebug123"
    new Debug\EnvDetector(), // the detection is performed from loaded ENV variables, the debug mode is enabled if a variable "DEBUG=1" is defined or if a variable "APP_ENV" has a different value than, "prod"
]);

// All your ENV variables are now accessible in the global arrays `$_ENV` and `$_SERVER`
```

If you're using [Nette Framework](https://nette.org) then you can in the bootstrap use this method instead:

```php
use SixtyEightPublishers\Environment\Bootstrap\EnvBootstrap;

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

EnvBootstrap::bootNetteConfigurator($configurator, __DIR__ . '/..', [
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
$ composer environment:dump <APP_ENV>

or shorter

$ composer env:dump <APP_ENV>
```

The file `.env.local.php` will be created in the application's root directory and it will be used instead of all your `.env` files.

## Contributing

Before committing any changes, don't forget to run

```bash
$ vendor/bin/php-cs-fixer fix --config=.php_cs.dist -v --dry-run
```

and

```bash
$ vendor/bin/tester ./tests
```

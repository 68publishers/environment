#!/usr/bin/env php
<?php

declare(strict_types=1);

use SixtyEightPublishers\Environment\Debug\CliOptionDetector;

require __DIR__ . '/../../vendor/autoload.php';

echo (new CliOptionDetector())->detect() ? 'yes' : 'no';

exit;

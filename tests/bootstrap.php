<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Stopwatch Component.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use DG\BypassFinals;

require_once __DIR__ . '/../vendor/autoload.php';

// Only the component's own classes: removing final and readonly from PHPUnit's classes breaks it.
$sourceDirectory = \realpath(__DIR__ . '/../src');
if (false === $sourceDirectory) {
    throw new RuntimeException('The src directory of the component cannot be found.');
}

BypassFinals::allowPaths([$sourceDirectory . '/*']);
BypassFinals::enable();

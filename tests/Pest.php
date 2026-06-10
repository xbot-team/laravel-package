<?php

/**
 * Pest.php — Pest PHP bootstrap.
 *
 * Registers the package's base TestCase so all test files in tests/
 * automatically extend it.
 */

use XBot\Package\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

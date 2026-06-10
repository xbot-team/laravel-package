<?php

use XBot\Package\Package;

/**
 * Package test suite.
 *
 * Tests the core Package class: instantiation, configuration
 * access, and the hello() greeting method.
 */
it('can be instantiated with default config', function (): void {
    $package = new Package;

    expect($package->hello())->toBe('Hello, World!');
});

it('can be instantiated with custom config', function (): void {
    $package = new Package(['name' => 'Laravel']);

    expect($package->hello())->toBe('Hello, Laravel!');
});

it('can retrieve configuration values', function (): void {
    $package = new Package(['name' => 'MyPkg', 'debug' => true]);

    expect($package->config('name'))->toBe('MyPkg')
        ->and($package->config('debug'))->toBeTrue()
        ->and($package->config('nonexistent'))->toBeNull()
        ->and($package->config('nonexistent', 'default'))->toBe('default');
});

it('returns full config when no key given', function (): void {
    $package = new Package(['name' => 'Test']);

    expect($package->config())->toBe(['name' => 'Test']);
});

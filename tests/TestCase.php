<?php

namespace XBot\Package\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use XBot\Package\PackageServiceProvider;

/**
 * Base TestCase for the package.
 *
 * Extends Orchestra Testbench so that the package's service provider
 * is registered in the testing Laravel application.
 */
class TestCase extends OrchestraTestCase
{
    /**
     * Register the package's service provider.
     *
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            PackageServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('package.name', 'Test');
        $app['config']->set('package.debug', true);
    }
}

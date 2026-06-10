<?php

declare(strict_types=1);

namespace XBot\Package;

use Illuminate\Support\ServiceProvider;
use XBot\Package\Console\Commands\PackageCommand;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * The config key for this package.
     */
    private const CONFIG_KEY = 'package';

    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/package.php',
            self::CONFIG_KEY
        );

        $this->app->singleton(Package::class, function ($app) {
            return new Package($app['config']->get(self::CONFIG_KEY, []));
        });

        $this->commands([
            PackageCommand::class,
        ]);
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/package.php' => config_path(self::CONFIG_KEY.'.php'),
            ], 'package-config');

            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations'),
            ], 'package-migrations');

            if (is_dir(__DIR__.'/../resources/views')) {
                $this->loadViewsFrom(__DIR__.'/../resources/views', 'package');
                $this->publishes([
                    __DIR__.'/../resources/views' => resource_path('views/vendor/package'),
                ], 'package-views');
            }

            if (is_dir(__DIR__.'/../resources/lang')) {
                $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'package');
                $this->publishes([
                    __DIR__.'/../resources/lang' => lang_path('vendor/package'),
                ], 'package-lang');
            }
        }
    }
}

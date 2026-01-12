<?php

namespace XBot\Package;

use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        # Register Package
        $this->app->singleton(Package::class, function ( $app ) {
            return new Package();
        });
        
        # Register Package Commands
        $this->commands([
            PackageCommand::class,
        ]);
    }
    
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

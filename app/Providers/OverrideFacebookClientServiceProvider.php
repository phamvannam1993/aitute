<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class OverrideFacebookClientServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('Facebook\Client', 'App\Vendor\Facebook\Client');
        
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

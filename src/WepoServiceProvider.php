<?php

namespace Wilcar\Wepo;

use Illuminate\Support\ServiceProvider;

class WepoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('wepo', function($app){
            return new Wepo;
        });

        $this->mergeConfigFrom(__DIR__.'/config/wepo.php', 'wepo');
    }

    public function boot()
    {
        //include __DIR__.'/../vendor/autoload.php';
        require __DIR__ . '/Http/routes.php';
        $this->publishes([__DIR__.'/config/wepo.php' => config_path('wepo.php')]);
    }
}

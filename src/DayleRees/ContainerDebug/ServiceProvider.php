<?php

namespace DayleRees\ContainerDebug;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    public function boot()
    {
        $this->app->instance('container.debug', new Command);
        $this->commands('container.debug');
    }

    public function register()
    {

    }
}

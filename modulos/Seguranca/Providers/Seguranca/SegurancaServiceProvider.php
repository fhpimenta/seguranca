<?php

namespace Modulos\Seguranca\Providers\Seguranca;

use Illuminate\Support\ServiceProvider;

class SegurancaServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Seguranca::class];
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Seguranca::class, function ($app) {
            return new Seguranca($app);
        });
    }
}

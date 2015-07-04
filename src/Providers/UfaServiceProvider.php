<?php

namespace Angejia\Ufa\Providers;

use Angejia\Ufa\Ufa;
use Illuminate\Support\ServiceProvider;

class UfaServiceProvider extends ServiceProvider {

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('pages.*', 'Angejia\Ufa\Composers\UfaComposer');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('UfaService', function($app) {
            return new Ufa();
        });
    }

}
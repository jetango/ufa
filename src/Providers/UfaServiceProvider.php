<?php namespace App\Providers;

use App\Helpers\Ufa;
use Illuminate\Support\ServiceProvider;

class UfaServiceProvider extends ServiceProvider {

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('pages.*', function ($view) {
            $data = $view->getData();
            if (! $data['title']) {
                $view->with('title', 'Test');
            }
            $view->with('test', 'IV');
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('UfaService', function($app)
        {
            return new Ufa();
        });
    }

}
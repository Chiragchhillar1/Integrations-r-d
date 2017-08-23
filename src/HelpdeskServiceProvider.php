<?php

namespace ChiragChhillar\Helpdesk;

use Illuminate\Support\ServiceProvider;

class HelpdeskServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        require __DIR__.'/routes/routes.php';

        $this->publishes([
        __DIR__.'/config/crms.php' => config_path('crms.php')
    ], 'config');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('helpdesk', function () {
        	return new Helpdesk();
        });
    }
}


<?php

namespace App\Providers;

use App\Support\UserValidator;
use Illuminate\Support\ServiceProvider;

class UserValidatorServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(UserValidator::class, function ($app) {
            return new UserValidator($this->app['validator'], $this->app['request']);
        });

        $this->app->alias(UserValidator::class, 'App\Contracts\Support\UserValidator');
    }
}

<?php

namespace App\Providers;

use GuzzleHttp\Client;
use App\Cache\CircuitBreaker;
use App\Http\Curl\HttpClient;
use App\Http\Curl\Facades\Facade;
use App\Http\Curl\ExceptionHandler;
use Illuminate\Support\ServiceProvider;

class CurlServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Facade::setHttpClient($this->app['App\Contracts\Http\Curl\HttpClient']);

        ExceptionHandler::setCircuitBreaker($this->app['App\Contracts\Cache\CircuitBreaker']);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCircuitBreaker();

        $this->registerHttpClient();
    }

    /**
     * Register the circuit breaker.
     *
     * @return void
     */
    protected function registerCircuitBreaker()
    {
        $this->app->singleton(CircuitBreaker::class, function ($app) {
            return new CircuitBreaker($app['cache']->store());
        });

        $this->app->alias(CircuitBreaker::class, 'App\Contracts\Cache\CircuitBreaker');
    }

    /**
     * Register the HTTP Client.
     *
     * @return void
     */
    protected function registerHttpClient()
    {
        $this->app->singleton(HttpClient::class, function ($app) {
            return new HttpClient(new Client, $app['App\Contracts\Cache\CircuitBreaker']);
        });

        $this->app->alias(HttpClient::class, 'App\Contracts\Http\Curl\HttpClient');
    }
}

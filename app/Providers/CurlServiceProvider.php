<?php

namespace App\Providers;

use GuzzleHttp\Client;
use App\Http\Curl\HttpClient;
use App\Http\Curl\Facades\Facade;
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
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerHttpClient();

        Facade::setHttpClient($this->app[HttpClient::class]);
    }

    /**
     * Register the HTTP Client.
     *
     * @return void
     */
    protected function registerHttpClient()
    {
        $this->app->singleton(HttpClient::class, function ($app) {
            return new HttpClient(new Client);
        });
    }
}

<?php

namespace App\Http\Curl\Facades;

use RuntimeException;
use App\Contracts\Http\Curl\HttpClient as HttpClientContract;

abstract class Facade
{
    /**
     * The service class name.
     *
     * @var string
     */
    protected $service;

    /**
     * The Http Client implementation.
     *
     * @var \App\Contracts\Http\Curl\HttpClient
     */
    protected static $httpClient;

    /**
     * Set the HttpClient for the Facade.
     *
     * @param  \App\Contracts\Http\Curl\HttpClient  $httpClient
     * @return void
     */
    public static function setHttpClient(HttpClientContract $httpClient)
    {
        static::$httpClient = $httpClient;
    }

    /**
     * Get the service instance.
     *
     * @return \App\Contracts\Http\Curl\Service
     */
    protected function getService()
    {
        $service = 'App\Http\Curl\Services\\'.$this->getServiceClassName().'Service';

        return new $service;
    }

    /**
     * Get the service class name.
     *
     * @return string
     */
    protected function getServiceClassName()
    {
        return $this->service ?: last(explode('\\', static::class));
    }

    /**
     * Get the endpoint instance.
     *
     * @param  string  $class
     * @param  array  $parameters
     * @return \App\Contracts\Http\Curl\Endpoint
     */
    protected function getEndpoint($class, array $parameters = [])
    {
        $endpoint = $this->getEndpointClassName($class);

        return new $endpoint($this->getService(), ...$parameters);
    }

    /**
     * Get the full endpoint class name by given class name.
     *
     * @param  string  $class
     * @return string
     */
    protected function getEndpointClassName($class)
    {
        return 'App\Http\Curl\Endpoints\\'.$this->getServiceClassName().'\\'.studly_case($class).'Endpoint';
    }

    /**
     * Handle dynamic method calls into the Facade.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, array $parameters)
    {
        if (! static::$httpClient instanceof HttpClientContract) {
            throw new RuntimeException('httpClient is not an instance of '.HttpClientContract::class.'.');
        }

        $endpoint = $this->getEndpoint($method, $parameters);

        return static::$httpClient->call($endpoint);
    }

    /**
     * Handle dynamic static method calls into the Facade.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, array $parameters)
    {
        return call_user_func_array([new static, $method], $parameters);
    }
}

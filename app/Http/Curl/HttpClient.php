<?php

namespace App\Http\Curl;

use GuzzleHttp\RequestOptions;
use App\Contracts\Http\Curl\Endpoint;
use GuzzleHttp\ClientInterface as GuzzleHttpClientContract;
use App\Contracts\Http\Curl\HttpClient as HttpClientContract;

class HttpClient implements HttpClientContract
{
    /**
     * The Guzzle HTTP Client implementation.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $httpClient;

    /**
     * Create a new HttpClient instance.
     *
     * @param  \GuzzleHttp\ClientInterface  $httpClient
     * @return void
     */
    public function __construct(GuzzleHttpClientContract $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Call an API by the given Endpoint object.
     *
     * @param  \App\Contracts\Http\Curl\Endpoint  $endpoint
     * @param  bool  $wait
     * @return mixed
     */
    public function call(Endpoint $endpoint, $wait = true)
    {
        $method = $wait ? 'request' : 'requestAsync';

        $result = $this->getClient()->{$method}(
            $endpoint->getMethod(), $endpoint->getUri(), $this->options($endpoint->getOptions())
        );

        return $wait ? new Response($result) : $result;
    }

    /**
     * Call an API by the given Endpoint object asynchronously.
     *
     * @param  \App\Contracts\Http\Curl\Endpoint  $endpoint
     * @return mixed
     */
    public function callAsync(Endpoint $endpoint)
    {
        return $this->call($endpoint, false);
    }

    /**
     * Get the HTTP Client implementation.
     *
     * @return \GuzzleHttp\ClientInterface
     */
    public function getClient()
    {
        return $this->httpClient;
    }

    /**
     * Returns the options when call an API.
     *
     * @param  array  $options
     * @return array
     */
    protected function options(array $options = [])
    {
        $defaults = [
            RequestOptions::CONNECT_TIMEOUT => 3,
            RequestOptions::HEADERS => ['Content-Type' => 'application/json'],
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::TIMEOUT => 5,
        ];

        return array_merge($defaults, $options);
    }

    /**
     * Handle dynamic method calls into the HttpClient.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, array $parameters)
    {
        return call_user_func_array([$this->getClient(), $method], $parameters);
    }
}

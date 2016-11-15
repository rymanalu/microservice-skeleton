<?php

namespace App\Http\Curl;

use GuzzleHttp\ClientInterface as GuzzleHttpClientContract;
use App\Contracts\Http\Curl\HttpClient as HttpClientContract;

/**
 * @todo Finish this class...
 */
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
     * Get the HTTP Client implementation.
     *
     * @return \GuzzleHttp\ClientInterface
     */
    public function getClient()
    {
        return $this->httpClient;
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

<?php

namespace App\Contracts\Http\Curl;

interface HttpClient
{
    /**
     * Get the HTTP Client implementation.
     *
     * @return \GuzzleHttp\ClientInterface
     */
    public function getClient();

    /**
     * Call an API by the given Endpoint object asynchronously.
     *
     * @param  \App\Contracts\Http\Curl\Endpoint  $endpoint
     * @return mixed
     */
    public function callAsync(Endpoint $endpoint);

    /**
     * Call an API by the given Endpoint object.
     *
     * @param  \App\Contracts\Http\Curl\Endpoint  $endpoint
     * @param  bool  $wait
     * @return mixed
     */
    public function call(Endpoint $endpoint, $wait = true);
}

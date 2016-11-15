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
}

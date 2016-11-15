<?php

namespace App\Http\Curl\Endpoints;

use GuzzleHttp\Psr7\Request;
use App\Contracts\Http\Curl\Service;

abstract class Endpoint
{
    /**
     * The endpoint URI.
     *
     * @var string
     */
    protected $uri;

    /**
     * The endpoint method.
     *
     * @var string
     */
    protected $method;

    /**
     * The Service implementation.
     *
     * @var \App\Contracts\Http\Curl\Service
     */
    protected $service;

    /**
     * Create a new Endpoint instance.
     *
     * @param  \App\Contracts\Http\Curl\Service  $service
     * @return void
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * Get the endpoint URI.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->normalize($this->service->uri()).'/'.$this->normalize($this->uri ?: '');
    }

    /**
     * Get the endpoint method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method ?: 'GET';
    }

    /**
     * Get the endpoint options (the format must be follow GuzzleHttpClient request options).
     *
     * @return array
     */
    public function getOptions()
    {
        return [];
    }

    /**
     * Get the Service implementation in this endpoint.
     *
     * @return \App\Contracts\Http\Curl\Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Normalize the given string.
     *
     * @param  string  $string
     * @return string
     */
    protected function normalize($string)
    {
        return trim($string, '/');
    }
}

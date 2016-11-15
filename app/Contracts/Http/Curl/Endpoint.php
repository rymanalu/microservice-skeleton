<?php

namespace App\Contracts\Http\Curl;

interface Endpoint
{
    /**
     * Get the endpoint URI.
     *
     * @return string
     */
    public function getUri();

    /**
     * Get the endpoint method.
     *
     * @return string
     */
    public function getMethod();

    /**
     * Get the endpoint options.
     *
     * @return array
     */
    public function getOptions();
}

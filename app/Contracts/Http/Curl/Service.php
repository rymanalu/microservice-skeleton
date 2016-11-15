<?php

namespace App\Contracts\Http\Curl;

interface Service
{
    /**
     * Get the microservice's base URI.
     *
     * @return string
     */
    public function uri();

    /**
     * Get the microservice's name.
     *
     * @return string
     */
    public function name();
}

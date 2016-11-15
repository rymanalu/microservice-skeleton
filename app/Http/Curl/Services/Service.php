<?php

namespace App\Http\Curl\Services;

use App\Contracts\Http\Curl\Service as ServiceContract;

abstract class Service implements ServiceContract
{
    /**
     * Get the microservice's base URI.
     *
     * @return string
     */
    abstract public function uri();

    /**
     * Get the microservice's name.
     *
     * @return string
     */
    abstract public function name();
}

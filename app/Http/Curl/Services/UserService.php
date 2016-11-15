<?php

namespace App\Http\Curl\Services;

use App\Contracts\Http\Curl\Service;

class UserService implements Service
{
    /**
     * Get the microservice's base URI.
     *
     * @return string
     */
    public function uri()
    {
        return env('MICROSERVICE_USER_URI');
    }

    /**
     * Get the microservice's name.
     *
     * @return string
     */
    public function name()
    {
        return 'Users Microservice';
    }
}

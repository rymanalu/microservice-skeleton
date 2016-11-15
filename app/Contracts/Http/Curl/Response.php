<?php

namespace App\Contracts\Http\Curl;

interface Response
{
    /**
     * Check if the call is successful by the response code.
     *
     * @return bool
     */
    public function isSuccessful();

    /**
     * Get the response body.
     *
     * @param  bool  $toArray
     * @return array|object
     */
    public function getBody($toArray = false);
}

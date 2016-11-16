<?php

namespace App\Http\Curl;

use GuzzleHttp\Exception\RequestException;

final class CurlHttpCode
{
    /**
     * Generate the HTTP code based on cURL error number in the given exception.
     *
     * @param  \GuzzleHttp\Exception\RequestException  $e
     * @return int
     */
    public static function generate(RequestException $e)
    {
        switch (static::errorNumber($e)) {
            case 7:
                return 503;
                break;
            case 28:
                return 504;
                break;
            default:
                return 500;
                break;
        }
    }

    /**
     * Get the cURL error number.
     *
     * @param  \GuzzleHttp\Exception\RequestException  $e
     * @return int
     */
    public static function errorNumber(RequestException $e)
    {
        return $e->getHandlerContext()['errno'];
    }
}

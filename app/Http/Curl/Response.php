<?php

namespace App\Http\Curl;

use Psr\Http\Message\ResponseInterface;
use App\Contracts\Http\Curl\Response as ResponseContract;

class Response implements ResponseContract
{
    /**
     * The HTTP Response implementation.
     *
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * Create a new Response instance.
     *
     * @param  \Psr\Http\Message\ResponseInterface  $response
     * @return void
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Check if the call is successful by the response code.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->response->getStatusCode() >= 200 && $this->response->getStatusCode() < 300;
    }

    /**
     * Get the response body.
     *
     * @param  bool  $toArray
     * @return array|object|null
     */
    public function getBody($toArray = false)
    {
        return json_decode($this->response->getBody(), $toArray);
    }

    /**
     * Handle dynamic method calls into the Response.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, array $parameters)
    {
        return call_user_func_array([$this->response, $method], $parameters);
    }
}

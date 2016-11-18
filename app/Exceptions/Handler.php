<?php

namespace App\Exceptions;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Curl\ExceptionHandler as CurlExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        log_exception($e);

        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof GuzzleException) {
            return $this->handlerHttpClientException($e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle the HTTP Client exception into an HTTP Response.
     *
     * @param  \GuzzleHttp\Exception\GuzzleException  $e
     * @return \Illuminate\Http\Response
     */
    protected function handlerHttpClientException(GuzzleException $e)
    {
        $handler = new CurlExceptionHandler($e);

        return $handler->handle();
    }
}

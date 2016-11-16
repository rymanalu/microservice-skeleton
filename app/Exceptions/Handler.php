<?php

namespace App\Exceptions;

use Exception;
use App\Http\Curl\CurlHttpCode;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
     * The exception handler mapping for the application.
     *
     * Example: 'ExceptionClassName' => 'MethodThatWillHandleTheExceptionInThisClass'
     *
     * @var array
     */
    protected $handler = [
        ConnectException::class => 'ConnectException',
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
        if (isset($this->handler[get_class($e)])) {
            return $this->handlerHttpClientException($e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle the HTTP Client exception into an HTTP Response.
     *
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    protected function handlerHttpClientException(Exception $e)
    {
        $method = $this->handler[get_class($e)];

        return $this->{'render'.$method}($e);
    }

    /**
     * Render an ConnectException into an HTTP response.
     *
     * @param  \GuzzleHttp\Exception\ConnectException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function renderConnectException(ConnectException $e)
    {
        return response_json(['message' => $this->buildErrorMessage($e)], $this->buildHttpCode($e));
    }

    /**
     * Build an error message by given exception.
     *
     * @param  \Exception  $e
     * @return string
     */
    protected function buildErrorMessage(Exception $e)
    {
        if ($e instanceof RequestException) {
            return trans('curl.'.CurlHttpCode::errorNumber($e));
        }

        return $e->getMessage();
    }

    /**
     * Build the HTTP code by given exception.
     *
     * @param  \Exception  $e
     * @return int
     */
    protected function buildHttpCode($e)
    {
        if ($e instanceof RequestException) {
            return CurlHttpCode::generate($e);
        }

        return 500;
    }
}

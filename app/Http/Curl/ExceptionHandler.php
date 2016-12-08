<?php

namespace App\Http\Curl;

use RuntimeException;
use App\Http\Curl\CurlHttpCode;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use App\Exceptions\CircuitBreakerException;
use Rymanalu\LaravelCircuitBreaker\CircuitBreakerInterface;

class ExceptionHandler
{
    /**
     * The GuzzleException implementation.
     *
     * @var \GuzzleHttp\Exception\GuzzleException
     */
    protected $e;

    /**
     * The CircuitBreaker implementation.
     *
     * @var \Rymanalu\LaravelCircuitBreaker\CircuitBreakerInterface
     */
    protected static $circuitBreaker;

    /**
     * The exception handler mapping for the application.
     *
     * Example: 'ExceptionClassName' => 'MethodThatWillHandleTheExceptionInThisClass'
     *
     * @var array
     */
    protected $handler = [
        ConnectException::class => 'ConnectException',
        CircuitBreakerException::class => 'CircuitBreakerException',
    ];

    /**
     * Create a new exception handler instance.
     *
     * @param  \GuzzleHttp\Exception\GuzzleException  $e
     * @return void
     */
    public function __construct(GuzzleException $e)
    {
        $this->e = $e;
    }

    /**
     * Handle the exception and return an HTTP response
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \RuntimeException
     */
    public function handle()
    {
        $e = get_class($this->e);

        if (! isset($this->handler[$e])) {
            throw new RuntimeException('Handler is unavailable for this exception ['.$e.'].');
        }

        $method = $this->handler[$e];

        return $this->{'render'.$method}($this->e);
    }

    /**
     * Set the CircuitBreaker instance for this class.
     *
     * @param  \Rymanalu\LaravelCircuitBreaker\CircuitBreakerInterface $circuitBreaker
     * @return void
     */
    public static function setCircuitBreaker(CircuitBreakerInterface $circuitBreaker)
    {
        static::$circuitBreaker = $circuitBreaker;
    }

    /**
     * Render the ConnectException into an HTTP response.
     *
     * @param  \GuzzleHttp\Exception\ConnectException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function renderConnectException(ConnectException $e)
    {
        return $this->toResponse($e);
    }

    /**
     * Render the CircuitBreakerException into an HTTP response.
     *
     * @param  \App\Exceptions\CircuitBreakerException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function renderCircuitBreakerException(CircuitBreakerException $e)
    {
        return $this->toResponse($e, ['message' => $e->getMessage()], 503);
    }

    /**
     * Render the given exception into an HTTP response.
     *
     * @param  \GuzzleHttp\Exception\GuzzleException  $e
     * @return \Illuminate\Http\Response
     */
    protected function toResponse(GuzzleException $e, array $data = [], $httpCode = 0)
    {
        return response_json(
            $data ?: ['message' => $this->buildErrorMessage($e)],
            $httpCode ?: $this->buildHttpCode($e)
        );
    }

    /**
     * Build an error message by given exception.
     *
     * @param  \GuzzleHttp\Exception\GuzzleException  $e
     * @return string
     */
    protected function buildErrorMessage(GuzzleException $e)
    {
        if ($e instanceof RequestException) {
            return trans('curl.'.CurlHttpCode::errorNumber($e));
        }

        return $e->getMessage();
    }

    /**
     * Build the HTTP code by given exception.
     *
     * @param  \GuzzleHttp\Exception\GuzzleException  $e
     * @return int
     */
    protected function buildHttpCode(GuzzleException $e)
    {
        if ($e instanceof RequestException) {
            $this->track($e, $code = CurlHttpCode::generate($e));

            return $code;
        }

        return 500;
    }

    /**
     * Tracks the error request.
     *
     * @param  \GuzzleHttp\Exception\GuzzleException  $e
     * @param  int  $code
     * @return void
     */
    protected function track(GuzzleException $e, $code)
    {
        if (static::$circuitBreaker instanceof CircuitBreakerInterface && $e instanceof RequestException && 503 == $code) {
            $key = $this->resolveRequestSignature($e);

            static::$circuitBreaker->track($key, env('CIRCUIT_BREAKER_DECAY', 1));
        }
    }

    /**
     * Resolve the signature for the error request.
     *
     * @param  \GuzzleHttp\Exception\RequestException  $e
     * @return string
     */
    protected function resolveRequestSignature(RequestException $e)
    {
        return sha1($e->getRequest()->getUri());
    }
}

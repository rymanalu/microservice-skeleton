<?php

namespace App\Cache;

use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository as Cache;
use App\Contracts\Cache\CircuitBreaker as CircuitBreakerContract;

class CircuitBreaker implements CircuitBreakerContract
{
    /**
     * The cache store implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Create a new circuit breaker instance.
     *
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     * @return void
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Determine if the given key has been "accessed" and return too many errors.
     *
     * @param  string  $key
     * @param  int  $maxErrors
     * @param  int  $decayMinutes
     * @return bool
     */
    public function tooManyErrors($key, $maxErrors, $decayMinutes = 1)
    {
        if ($this->cache->has($key.':service_lockout')) {
            return true;
        }

        if ($this->errors($key) > $maxErrors) {
            $this->cache->add($key.':service_lockout', Carbon::now()->getTimestamp() + ($decayMinutes * 60), $decayMinutes);

            $this->resetErrors($key);

            return true;
        }

        return false;
    }

    /**
     * Increment the counter for a given key for a given decay time.
     *
     * @param  string  $key
     * @param  float|int  $decayMinutes
     * @return int
     */
    public function track($key, $decayMinutes = 1)
    {
        $this->cache->add($key, 1, $decayMinutes);

        return (int) $this->cache->increment($key);
    }

    /**
     * Get the number of errors for the given key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function errors($key)
    {
        return $this->cache->get($key, 0);
    }

    /**
     * Reset the number of errors for the given key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function resetErrors($key)
    {
        return $this->cache->forget($key);
    }
}

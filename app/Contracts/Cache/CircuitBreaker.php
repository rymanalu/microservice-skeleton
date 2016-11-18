<?php

namespace App\Contracts\Cache;

interface CircuitBreaker
{
    /**
     * Determine if the given key has been "accessed" and return too many errors.
     *
     * @param  string  $key
     * @param  int  $maxErrors
     * @param  int  $decayMinutes
     * @return bool
     */
    public function tooManyErrors($key, $maxErrors, $decayMinutes = 1);

    /**
     * Increment the counter for a given key for a given decay time.
     *
     * @param  string  $key
     * @param  float|int  $decayMinutes
     * @return int
     */
    public function track($key, $decayMinutes = 1);

    /**
     * Get the number of errors for the given key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function errors($key);

    /**
     * Reset the number of errors for the given key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function resetErrors($key);
}

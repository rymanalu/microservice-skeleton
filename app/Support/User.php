<?php

namespace App\Support;

use Illuminate\Contracts\Support\Jsonable;

/**
 * This class only to wrap the user from User microservice.
 */
class User implements Jsonable
{
    /**
     * The user's attributes.
     *
     * @var array
     */
    protected $attributes;

    /**
     * Create a new User instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * Convert the user instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->attributes, $options);
    }

    /**
     * Dynamically retrieve user's attributes.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    /**
     * Dynamically set user's attributes.
     *
     * @param  string  $key
     * @param  mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Convert the user to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}

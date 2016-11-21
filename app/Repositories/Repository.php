<?php

namespace App\Repositories;

use App\Contracts\Repositories\Repository as RepositoryContract;

abstract class Repository implements RepositoryContract
{
    /**
     * The model instance being used.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setModel();
    }

    /**
     * Get the model instance being used.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Specify model's class name.
     *
     * @return string
     */
    abstract protected function model();

    /**
     * Set the model instance for the repository.
     *
     * @return void
     */
    protected function setModel()
    {
        $model = $this->model();

        $this->model = new $model;
    }

    /**
     * Handle dynamic method calls into the repository.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->getModel(), $method], $parameters);
    }

    /**
     * Handle dynamic static method calls into the repository.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return call_user_func_array([new static, $method], $parameters);
    }
}

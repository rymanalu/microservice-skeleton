<?php

namespace App\Contracts\Repositories;

interface Repository
{
    /**
     * Get the model instance being used.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel();
}

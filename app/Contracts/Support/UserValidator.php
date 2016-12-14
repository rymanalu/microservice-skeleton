<?php

namespace App\Contracts\Support;

interface UserValidator
{
    /**
     * Determine if the user's data passes the validation rules.
     *
     * @return bool
     */
    public function passes();

    /**
     * Get the user attributes in the response.
     *
     * @return array
     */
    public function getUser();

    /**
     * Determine if the token is valid.
     *
     * @return bool
     */
    public function tokenIsValid();

    /**
     * Determine if the token is present.
     *
     * @return bool
     */
    public function tokenIsPresent();
}

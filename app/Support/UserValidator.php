<?php

namespace App\Support;

use App\Http\Curl\Facades\User as UserMicroservice;
use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use App\Contracts\Support\UserValidator as UserValidatorContract;

class UserValidator implements UserValidatorContract
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The HTTP response from Check Token API.
     *
     * @var array
     */
    protected $response;

    /**
     * The ValidatorFactory implementation.
     *
     * @var \Illuminate\Contracts\Validation\Factory
     */
    protected $validator;

    /**
     * Create a new UserValidator instance.
     *
     * @param  \Illuminate\Contracts\Validation\Factory
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(ValidatorFactory $validator, $request)
    {
        $this->request = $request;
        $this->validator = $validator;
    }

    /**
     * Determine if the user's data passes the validation rules.
     *
     * @return bool
     */
    public function passes()
    {
        return $this->tokenIsPresent() && $this->tokenIsValid();
    }

    /**
     * Get the user attributes in the response.
     *
     * @return array
     */
    public function getUser()
    {
        return $this->response['user'];
    }

    /**
     * Determine if the token is valid.
     *
     * @return bool
     */
    public function tokenIsValid()
    {
        $validator = $this->validator->make($this->request->all(), ['token' => 'required']);

        return $validator->passes();
    }

    /**
     * Determine if the token is present.
     *
     * @return bool
     */
    public function tokenIsPresent()
    {
        $result = UserMicroservice::checkToken(['json' => ['token' => $this->request->input('token')]]);

        $this->response = $result->getBody(true);

        return $result->isSuccessful();
    }
}

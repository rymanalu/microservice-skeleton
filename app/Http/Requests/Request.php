<?php

namespace App\Http\Requests;

use Validator;
use Illuminate\Http\Request as BaseRequest;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;

abstract class Request extends BaseRequest implements ValidatesWhenResolved
{
    /**
     * Determine if the requester is authorized to make this request.
     *
     * @return bool
     */
    abstract public function authorize();

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules();

    /**
     * Validate the class instance.
     *
     * @return void
     */
    public function validate()
    {
        $validator = $this->getValidatorInstance();

        if (! $this->authorize()) {
            $this->failedAuthorization();
        } elseif ($validator->fails()) {
            $this->failedValidation($validator);
        }
    }

    /**
     * Get the response for a forbidden operation.
     *
     * @return \Illuminate\Http\Response
     */
    public function forbiddenResponse()
    {
        return response_json(['message' => 'Forbidden'], 403);
    }

    /**
     * Get the proper failed validation response for the request.
     *
     * @param  array  $errors
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(array $errors)
    {
        return response_json($errors, 422);
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Http\Exception\HttpResponseException
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException($this->forbiddenResponse());
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exception\HttpResponseException
     */
    protected function failedValidation(ValidatorContract $validator)
    {
        throw new HttpResponseException($this->response($this->formatErrors($validator)));
    }

    /**
     * Format the errors from the given Validator instance.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return array
     */
    protected function formatErrors(ValidatorContract $validator)
    {
        return ['message' => implode(', ', $validator->errors()->all())];
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        return Validator::make($this->all(), $this->rules());
    }
}

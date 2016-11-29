<?php

namespace App\Http\Middleware;

use Closure;
use Validator;
use App\Http\Curl\Facades\User;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->tokenIsPresent($request) && $this->tokenIsValid($request)) {
            return $next($request);
        }

        return response_json(['message' => 'Given token is not present or invalid.'], 403);
    }

    /**
     * Check if the token is present in the current request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokenIsPresent($request)
    {
        $validator = Validator::make($request->all(), ['token' => 'required']);

        return $validator->passes();
    }

    /**
     * Check if the token is valid.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokenIsValid($request)
    {
        $result = User::checkToken(['json' => ['token' => $request->input('token')]]);

        return $result->isSuccessful();
    }
}

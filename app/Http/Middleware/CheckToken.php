<?php

namespace App\Http\Middleware;

use Closure;
use Validator;
use App\Http\Curl\Facades\User;
use App\Support\User as UserObject;

class CheckToken
{
    /**
     * The response body from check token API.
     *
     * @var array
     */
    protected $checkTokenResponseBody;

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
            $this->addUserToRequest($request);

            return $next($request);
        }

        return response_json(['message' => 'Given token is not present or invalid.'], 403);
    }

    /**
     * Add the authenticated user object to current request.
     *
     * @param  \Illuminate\Http\Request  $token
     * @return void
     */
    protected function addUserToRequest($request)
    {
        $request->request->add(['user' => new UserObject($this->checkTokenResponseBody['user'])]);
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

        $this->checkTokenResponseBody = $result->getBody(true);

        return $result->isSuccessful();
    }
}

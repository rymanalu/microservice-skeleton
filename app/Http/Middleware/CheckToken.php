<?php

namespace App\Http\Middleware;

use Closure;
use App\Support\User as UserObject;
use App\Contracts\Support\UserValidator;

class CheckToken
{
    /**
     * The UserValidator implementation.
     *
     * @var \App\Contracts\Support\UserValidator
     */
    protected $userValidator;

    /**
     * Create a new middleware instance.
     *
     * @param  \App\Contracts\Support\UserValidator  $userValidator
     */
    public function __construct(UserValidator $userValidator)
    {
        $this->userValidator = $userValidator;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->userValidator->passes()) {
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
        $request->request->add(['user' => new UserObject($this->userValidator->getUser())]);
    }
}

# Lumen Microservice Skeleton

- [About](#about)
- [Installation](#installation)
- [Configuration](#configuration)
- [Features](#features)
    - [Calling other microservices](#calling-other-microservices)
    - [Circuit Breaker](#circuit-breaker)
    - [Requests Limiter](#requests-limiter)

<a name="about"></a>
## About

This is a microservice skeleton, using Laravel Lumen (v5.1 LTS) written in PHP.

<a name="installation"></a>
## Installation

1. Clone this repo.
2. Move to the cloned folder.
3. Run `composer install`.
4. Set all required settings in `.env`.

<a name="configuration"></a>
## Configuration

Set this following variables in `.env` as you want:

1. `CONNECT_TIMEOUT`: the number in seconds to wait while trying to connect to a microservice.
2. `TIMEOUT`: describing the timeout of the request in seconds.
3. `CIRCUIT_BREAKER_MAX`: the maximum number of failed request before we breake the next request in specified period.
4. `CIRCUIT_BREAKER_DECAY`: the period in minutes to open the breaker.

<a name="features"></a>
## Features

The following features here are the basic features to provide microservice pattern:

<a name="calling-other-microservices"></a>
### Calling other microservices

This skeleton provide a simple way to call other microservices. Just define the service and all the endpoints. And yeah, you can also create the facade for easing this task.

PS: This skeleton use Guzzle (`guzzlehttp/guzzle`) to send the HTTP requests.

#### Services

The **Services** jargon here means all your other microservices. Define your services in `app/Http/Curl/Services` by creating a new class that must implements the `App\Contracts\Http\Curl\Service` interface.

The class name must use **Service** suffix. Example: `UserService` represents the user microservice.

#### Endpoints

Define all the endpoints by grouping it to a folder where the folder's name is same with the service and place them to `app/Http/Curl/Endpoints` folder.

For example, we have user microservice that has check token endpoint. So we create a new folder named **User**, and create a new class like this: `CheckTokenEndpoint` (always use **Endpoint** suffix).

#### Facades

Facade provide an elegant and simple way to call other microservice. Just create a new class named like your service (without the **Service** suffix) in `app/Http/Curl/Facades` that must extends the `App\Http\Curl\Facades\Facade` class.

For example: `App\Http\Curl\Facades\User` for `App\Http\Curl\Services\UserService`.

#### How To Call

This skeleton has example classes that I've explained above. If you open `app/Http/Middleware/CheckToken.php`, you will see that I use the `App\Http\Curl\Facades\User` facade to call the check token endpoint in line 48: `User::checkToken(['token' => $request->input('token')])`.

That block means that I call the check token endpoint (`App\Http\Curl\Endpoints\User\CheckTokenEndpoint`) that belongs to user microservice (`App\Http\Curl\Services\UserService`).

Another example, if you have get profile endpoint that belongs to user microservice, create a new endpoint class named `GetProfileEndpoint`. Then call the endpoint like this: `User::getProfile()`.

If you must send some data to the endpoint, just pass it in an associative array. Example, you must send the user's id when call get profile: `User::getProfile(['id' => 12])`. Of course, the keys of the associative array must follows the requirements of the endpoint. If the endpoint needs `user_id` parameter, then changed it to `User::getProfile(['user_id' => 12])`.

#### Responses

The returned HTTP response will become a `App\Http\Curl\Response` object that has some convenient methods, like `isSuccessful()` and `getBody($toArray = false)` (check each docblock for the description).

And yes, you still can calls the GuzzleHttp Response methods automagically from this object.

<a name="circuit-breaker"></a>
### Circuit Breaker

Circuit Breaker is a pattern to track the failed requests. It will automatically break the next request if the number of failed request in specified period is large than our configured maximum error. Then it will retry to connect to the service after the period timeouts. If we still got the error, we will track and break if the error rate exceeds. Or if the connection is success, it will open the breaker.

You don't have to implement this when [calling other microservices](#calling-other-microservices), because we already provided.

<a name="requests-limiter"></a>
### Requests Limiter

Yes, we must prevent a client to make unlimited request in a period. Just use the `throttle` middleware (`App\Http\Middleware\ThrottleRequests`) and specify the maximum attempts and the decay minutes. Example:

```php
// app/Http/routes.php

$app->group(['middleware' => 'throttle:60,1'], function () {
    //
});
```

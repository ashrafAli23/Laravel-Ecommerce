<?php

declare(strict_types=1);

namespace Modules\Auth\traits;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

trait ThrottlesLogins
{
    /**
     * @param Request $request
     * @return boolean
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            $this->maxAttempts()
        );
    }

    /**
     * Increment the login attempts for the user.
     *
     * @param Request $request
     * @return void
     */
    protected function incrementLoginAttempts(Request $request)
    {
        $this->limiter()->hit(
            $this->throttleKey($request),
            $this->decayMinutes() * 60
        );
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param Request $request
     * @return void
     *
     * @throws ValidationException
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        throw ValidationException::withMessages([
            $this->username() => [
                Lang::get('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ],
        ])->status(Response::HTTP_TOO_MANY_REQUESTS);
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param Request $request
     * @return void
     */
    protected function clearLoginAttempts(Request $request)
    {
        $this->limiter()->clear($this->throttleKey($request));
    }

    /**
     * Fire an event when a lockout occurs.
     *
     * @param Request $request
     * @return void
     */
    protected function fireLockoutEvent(Request $request)
    {
        event(new Lockout($request));
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param Request $request
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input($this->username())) . '|' . $request->ip();
    }

    /**
     * Get the rate limiter instance.
     *
     * @return RateLimiter
     */
    protected function limiter()
    {
        return app(RateLimiter::class);
    }

    /**
     * Get the maximum number of attempts to allow.
     *
     * @return int
     */
    public function maxAttempts()
    {
        return $this->maxAttempts;
    }

    /**
     * Get the number of minutes to throttle for.
     *
     * @return int
     */
    public function decayMinutes()
    {
        return $this->decayMinutes;
    }
}

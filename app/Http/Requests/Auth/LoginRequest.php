<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        $this->ensureIsNotRateLimited();

        // validate credentials against JWT server requirements:

        $credentials = $this->validate([
            'email' => 'email|required',
            'password' => 'min:6|required',
        ]);


        // TODO: is it ok to post password without hashing?
        //check credentials against external server
        $response = Http::post(config('auth.auth_url').'/api/user/login', $credentials);


        //If cannot authenticate
        if (! $response->ok()) {
            RateLimiter::hit($this->throttleKey());

            //dd('auth failed');
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // set leeway to account for time diff?
        JWT::$leeway = 10;

        // decode token to get User info
        // $token = JWTAuth::getToken();
        $token = $response->body();
        $decoded = JWT::decode($token, config('auth.auth_secret'), ['alg' => 'HS256']);

        //If user is not in system, store:
        $user = User::updateOrCreate(
            ['id' => $decoded->_id],
            [
                'email' => $credentials['email'],
                'username' => $credentials['email'],
                'jwt_token' => $token,
            ],
        );

        RateLimiter::clear($this->throttleKey());

        Auth::login($user);

    }

    /**
     * Attempt to authenticate using a JWT shared from an external application
     */
    public function authenticateFromExternal() {
         // set leeway to account for time diff?
        JWT::$leeway = 10;

        // decode token to get User info
        // $token = JWTAuth::getToken();
        $token = $this->input('token');
        $decoded = JWT::decode($token, config('auth.auth_secret'), ['alg' => 'HS256']);

        //If user is not in system, store:
        $user = User::updateOrCreate(
            ['id' => $decoded->_id],
            [
                'email' => $decoded->email,
                'username' => $decoded->email,
                'jwt_token' => $token,
            ],
        );

        RateLimiter::clear($this->throttleKey());
        Auth::login($user);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('email')).'|'.$this->ip();
    }
}

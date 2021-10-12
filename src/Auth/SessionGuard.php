<?php

namespace Radiate\Auth;

use Radiate\Auth\Contracts\StatefulGuard;
use Illuminate\Support\Traits\Macroable;
use Radiate\Auth\Contracts\SupportsBasicAuth;
use Radiate\Auth\Contracts\UserProvider;
use Radiate\Http\Request;
use WP_User;

class SessionGuard implements StatefulGuard, SupportsBasicAuth
{
    use GuardHelpers, Macroable;

    /**
     * The request
     *
     * @var \Radiate\Http\Request
     */
    protected $request;


    /**
     * Create a new authentication guard.
     *
     * @param  \Radiate\Auth\Contracts\UserProvider  $provider
     * @param  \Radiate\Http\Request  $request
     * @return void
     */
    public function __construct(UserProvider $provider, Request $request)
    {
        $this->provider = $provider;
        $this->request = $request;
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return is_user_logged_in();
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|string|null
     */
    public function id()
    {
        return ($id = get_current_user_id()) !== 0 ? $id : null;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Radiate\Auth\Contracts\Authenticatable|\WP_User|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        if ($id = $this->id()) {
            return $this->user = $this->provider->retrieveById($id);
        }
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        if ($this->provider->retrieveByCredentials($credentials)) {
            return true;
        }

        return false;
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array  $credentials
     * @param  bool  $remember
     * @return bool
     */
    public function attempt(array $credentials = [], bool $remember = false)
    {
        if ($user = $this->provider->retrieveByCredentials($credentials)) {
            $this->login($user, $remember);

            return true;
        }

        return false;
    }

    /**
     * Log a user into the application.
     *
     * @param  \Radiate\Auth\Contracts\Authenticatable|\WP_User  $user
     * @param  bool  $remember
     * @return void
     */
    public function login($user, bool $remember = false)
    {
        $id = $this->getAuthIdentifier($user);

        wp_clear_auth_cookie();
        wp_set_auth_cookie($id, $remember);

        $this->setUser($user);
    }

    /**
     * Log the given user ID into the application.
     *
     * @param  int|string|null  $id
     * @param  bool  $remember
     * @return bool
     */
    public function loginUsingId($id, bool $remember = false)
    {
        if ($user = $this->provider->retrieveById($id)) {
            $this->login($user, $remember);

            return true;
        }

        return false;
    }

    /**
     * Log a user into the application without sessions or cookies.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function once(array $credentials = [])
    {
        if ($user = $this->provider->retrieveByCredentials($credentials)) {
            $this->setUser($user);

            return true;
        }

        return false;
    }

    /**
     * Log the given user ID into the application without sessions or cookies.
     *
     * @param  int|string|null  $id
     * @return bool
     */
    public function onceUsingId($id)
    {
        if ($user = $this->provider->retrieveById($id)) {
            $this->setUser($user);

            return true;
        }

        return false;
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        $this->user = null;

        wp_logout();
    }

    /**
     * Set the current user.
     *
     * @param  \Radiate\Auth\Contracts\Authenticatable|\WP_User  $user
     * @return static
     */
    public function setUser($user)
    {
        $this->user = $user;

        wp_set_current_user($this->getAuthIdentifier($user));

        return $this;
    }

    /**
     * Attempt to authenticate using HTTP Basic Auth.
     *
     * @param  string  $field
     * @return null
     *
     * @throws \Radiate\Auth\AuthorizationException
     */
    public function basic(string $field = 'email')
    {
        if ($this->check() || $this->attemptBasic($this->getRequest(), $field)) {
            return;
        }

        $this->failedBasicResponse();
    }

    /**
     * Attempt to authenticate using basic authentication.
     *
     * @param  \Radiate\Http\Request  $request
     * @param  string  $field
     * @return bool
     */
    protected function attemptBasic(Request $request, string $field)
    {
        if (!$request->getUser()) {
            return false;
        }

        return $this->attempt($this->basicCredentials($request, $field));
    }

    /**
     * Perform a stateless HTTP Basic login attempt.
     *
     * @param  string  $field
     * @return null
     *
     * @throws \Radiate\Auth\AuthorizationException
     */
    public function onceBasic($field = 'email')
    {
        if ($this->attemptOnceBasic($this->getRequest(), $field)) {
            return null;
        }

        return $this->failedBasicResponse();
    }

    /**
     * Attempt to authenticate using basic authentication.
     *
     * @param  \Radiate\Http\Request  $request
     * @param  string  $field
     * @return bool
     */
    protected function attemptOnceBasic(Request $request, string $field)
    {
        if (!$request->getUser()) {
            return false;
        }

        return $this->once($this->basicCredentials($request, $field));
    }

    /**
     * Get the current request instance.
     *
     * @return \Radiate\Http\Request
     */
    public function getRequest()
    {
        return $this->request ?: Request::capture();
    }

    /**
     * Get the credential array for an HTTP Basic request.
     *
     * @param  \Radiate\Http\Request  $request
     * @param  string  $field
     * @return array
     */
    protected function basicCredentials(Request $request, string $field)
    {
        return [$field => $request->getUser(), 'password' => $request->getPassword()];
    }

    /**
     * Get the response for basic authentication.
     *
     * @return void
     *
     * @throws \Radiate\Auth\AuthorizationException
     */
    protected function failedBasicResponse()
    {
        throw new AuthorizationException(
            'Invalid basic credentials.',
            403,
            ['www-authenticate' => 'Basic']
        );
    }
}

<?php

namespace Radiate\Routing;

use DateTime;
use Illuminate\Support\Traits\Macroable;
use Radiate\Http\Request;
use Radiate\Support\Arr;

class UrlGenerator
{
    use Macroable;

    /**
     * The request instance
     *
     * @var \Radiate\Http\Request
     */
    protected $request;

    /**
     * The asset root
     *
     * @var string|null
     */
    protected $assetRoot;

    /**
     * The encryption key resolver callable.
     *
     * @var callable
     */
    protected $keyResolver;

    /**
     * Assign the request object to the instance.
     *
     * @param \Radiate\Http\Request $request
     * @param string|null $assetRoot
     */
    public function __construct(Request $request, ?string $assetRoot = null)
    {
        $this->request = $request;
        $this->assetRoot = $assetRoot;
    }

    /**
     * Get the current URL without query parameters.
     *
     * @return string
     */
    public function current(): string
    {
        return $this->request->url();
    }

    /**
     * Get the current URL including query parameters.
     *
     * @return string
     */
    public function full(): string
    {
        return $this->request->fullUrl();
    }

    /**
     * Get the URL for the previous request.
     *
     * @param string|null $fallback The fallback URL
     * @return string
     */
    public function previous(?string $fallback = null): string
    {
        if ($previous = wp_get_referer()) {
            return $previous;
        }

        if ($fallback) {
            return $fallback;
        }

        return $this->home();
    }

    /**
     * Return the registration URL
     *
     * @param string $redirect The Redirect URL
     * @return string
     */
    public function register(string $redirect = ''): string
    {
        if ($redirect) {
            return add_query_arg('redirect_to', urlencode($redirect), wp_registration_url());
        }

        return wp_registration_url();
    }

    /**
     * Return the login URL
     *
     * @param string $redirect The redirect URL
     * @return string
     */
    public function login(string $redirect = '/'): string
    {
        return wp_login_url($redirect);
    }

    /**
     * Return the logout URL
     *
     * @param string $redirect The redirect URL
     * @return string
     */
    public function logout(string $redirect = '/'): string
    {
        return wp_logout_url($redirect);
    }

    /**
     * Return the privacy policy page if it is published.
     *
     * @return string
     */
    public function privacyPolicy(): string
    {
        return get_privacy_policy_url();
    }

    /**
     * Return the home URL
     *
     * @param string $path The path to append to the home URL
     * @param array $parameters The parameters to append to the home URL
     * @return string
     */
    public function home(string $path = '', array $parameters = []): string
    {
        return home_url($path . $this->formatParameters($parameters));
    }

    /**
     * Return the URL to the path specified
     *
     * @param string $path The path to append to the home URL
     * @param array $parameters parameters to pass to the URL
     * @return string
     */
    public function to(string $path, array $parameters = []): string
    {
        $path =  $path . $this->formatParameters($parameters);

        if ($this->isValidUrl($path)) {
            return $path;
        }

        return $this->home($path);
    }

    /**
     * Redirect to another page, with an optional status code
     *
     * @param string  $url    The URL to redirect to
     * @param integer $status The status code to send
     * @return void
     */
    public function redirect(string $url, int $status = 302): void
    {
        die(wp_redirect($url, $status));
    }

    /**
     * Return the admin URL
     *
     * @param string $path The path to append to the admin URL
     * @param array $parameters The parameters to append to the admin URL
     * @return string
     */
    public function admin(string $path = '', array $parameters = []): string
    {
        return admin_url($path . $this->formatParameters($parameters));
    }

    /**
     * Return the ajax URL
     *
     * @param string $action The ajax action
     * @param array $parameters The parameters to append to the ajax URL
     * @return string
     */
    public function ajax(string $action = '', array $parameters = []): string
    {
        $parameters['action'] = $action;

        return $this->admin('admin-ajax.php', $parameters);
    }

    /**
     * Return the REST URL
     *
     * @param string $path The path to append to the admin URL
     * @param array $parameters The parameters to append to the rest URL
     * @return string
     */
    public function rest(string $path = '', array $parameters = []): string
    {
        return rest_url($path . $this->formatParameters($parameters));
    }

    /**
     * Determine if the given path is a valid URL.
     *
     * @param  string  $path
     * @return bool
     */
    public function isValidUrl(string $path): bool
    {
        if (!preg_match('~^(#|//|https?://|(mailto|tel|sms):)~', $path)) {
            return filter_var($path, FILTER_VALIDATE_URL) !== false;
        }

        return true;
    }

    /**
     * Return a formatted query string
     *
     * @param array $parameters
     * @return string
     */
    public function formatParameters(array $parameters = []): string
    {
        return $parameters ? '?' . http_build_query($parameters) : '';
    }

    /**
     * Generate the URL to an application asset.
     *
     * @param string $path
     * @return string
     */
    public function asset(string $path): string
    {
        if ($this->isValidUrl($path)) {
            return $path;
        }

        $root = $this->assetRoot ?? site_url();

        return trim($root, '/') . '/' . trim($path, '/');
    }





    /**
     * Determine if the request URL has a valid signature
     *
     * @param \Radiate\Http\Request $request
     * @return boolean
     */
    public function hasValidSignature(Request $request)
    {
        return $this->hasCorrectSignature($request)
            && $this->signatureHasNotExpired($request);
    }

    /**
     * Determine if the request URL has a correct signature
     *
     * @param \Radiate\Http\Request $request
     * @return boolean
     */
    public function hasCorrectSignature(Request $request)
    {
        $original = rtrim($request->url() . '?' . Arr::query(
            Arr::except($request->query(), 'signature')
        ), '?');

        $signature = hash_hmac('sha256', $original, call_user_func($this->keyResolver));

        return hash_equals($signature, (string) $request->query('signature', ''));
    }

    /**
     * Determine if the request URL is expired
     *
     * @param \Radiate\Http\Request $request
     * @return boolean
     */
    public function signatureHasNotExpired(Request $request)
    {
        $expires = $request->query('expires');

        return !($expires && (new DateTime())->getTimestamp() > $expires);
    }

    /**
     * Create a signed URL
     *
     * @param string $path
     * @param array $parameters
     * @param integer|null $expiration
     * @return string
     */
    public function signedUrl(string $path, array $parameters = [], ?int $expiration = null): string
    {
        ksort($parameters);

        if ($expiration) {
            $parameters['expires'] = (new DateTime())->getTimestamp() + $expiration;
        }

        $key = call_user_func($this->keyResolver);

        $parameters['signature'] = hash_hmac('sha256', $this->to($path, $parameters), $key);

        return $this->to($path, $parameters);
    }

    /**
     * Get a temporary signed URL
     *
     * @param string $path
     * @param integer $expiration
     * @param array $parameters
     * @return string
     */
    public function temporarySignedUrl(string $path, int $expiration, array $parameters = []): string
    {
        return $this->signedUrl($path, $parameters, $expiration);
    }

    /**
     * Set the encryption key resolver.
     *
     * @param callable $keyResolver
     * @return static
     */
    public function setKeyResolver(callable $keyResolver)
    {
        $this->keyResolver = $keyResolver;

        return $this;
    }
}

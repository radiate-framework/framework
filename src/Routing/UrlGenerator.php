<?php

namespace Radiate\Routing;

use Illuminate\Support\Traits\Macroable;
use Radiate\Http\Request;

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
     * @return string
     */
    public function home(string $path = ''): string
    {
        return home_url($path);
    }

    /**
     * Return the URL to the path specified
     *
     * @param string $path The path to append to the home URL
     * @return string
     */
    public function to(string $path): string
    {
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
     * @return string
     */
    public function admin(string $path = ''): string
    {
        return admin_url($path);
    }

    /**
     * Return the ajax URL
     *
     * @param string $action The ajax action
     * @return string
     */
    public function ajax(string $action = ''): string
    {
        return $this->admin('admin-ajax.php' . ($action ? '?action=' . $action : ''));
    }

    /**
     * Return the REST URL
     *
     * @param string $path The path to append to the admin URL
     * @return string
     */
    public function rest(string $path = ''): string
    {
        return rest_url($path);
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
}

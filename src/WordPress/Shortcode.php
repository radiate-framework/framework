<?php

namespace Radiate\WordPress;

use Radiate\Foundation\Application;
use Radiate\View\View;

abstract class Shortcode
{
    /**
     * The shortcode name
     *
     * @var string
     */
    protected $name;

    /**
     * An array of allowed attributes and defaults
     *
     * @var array
     */
    protected $defaultAttributes = [];

    /**
     * The shortcode attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The shortcode content
     *
     * @var string
     */
    protected $content;

    /**
     * The app instance
     *
     * @var \Radiate\Foundation\Application
     */
    protected $app;

    /**
     * The view instance
     *
     * @var \Radiate\View\View
     */
    protected $view;

    /**
     * Create the shortcode instance
     *
     * @param \Radiate\Foundation\Application $app
     * @param \Radiate\View\View $view
     */
    public function __construct(Application $app, View $view)
    {
        $this->app = $app;
        $this->view = $view;
    }

    /**
     * Determine if the shortcode exists
     *
     * @return bool
     */
    protected function exists()
    {
        return shortcode_exists($this->name);
    }

    /**
     * Register the shortcode
     *
     * @return void
     */
    public function register()
    {
        if (!$this->exists()) {
            add_shortcode($this->name, function ($attrs, $content) {
                return $this->bindAttributes($attrs, $content);
            });
        }
    }

    /**
     * Merge the shortcode attributes and then call the handler
     *
     * @param array|string|null $attrs
     * @param string|null $content
     * @return mixed
     */
    protected function bindAttributes($attrs, string $content = '')
    {
        $this->attributes = shortcode_atts(
            $this->defaultAttributes,
            $attrs,
            $this->name
        );

        $this->content = $content;

        return $this->do($this->app->call([$this, 'handle']));
    }

    /**
     * make a view
     *
     * @param string $path
     * @param array $attrs
     * @return string
     */
    protected function view(string $path, array $attrs = [])
    {
        return $this->view->make($path, array_merge($attrs, ['shortcode' => $this]));
    }

    /**
     * get the attributes
     *
     * @return array
     */
    public function attributes()
    {
        return $this->attributes;
    }

    /**
     * Get the content
     *
     * @return string
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * Get an attribute
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->attributes[$key];
    }

    /**
     * Dynamically get the shortcode attributes
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * Call the shortcode
     *
     * @param string|null $shortcode
     * @return string
     */
    public function do(?string $shortcode)
    {
        return do_shortcode($shortcode);
    }
}

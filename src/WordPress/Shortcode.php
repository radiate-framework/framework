<?php

namespace Radiate\WordPress;

use Radiate\Support\Facades\View;

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
     * Create the shortcode instance
     */
    public function __construct()
    {
        $this->register();
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
    protected function register()
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

        return $this->do($this->handle());
    }

    /**
     * handle the shortcode
     *
     * @return mixed
     */
    abstract protected function handle();

    /**
     * make a view
     *
     * @param string $path
     * @param array $attrs
     * @return string
     */
    protected function view(string $path, array $attrs = [])
    {
        return View::make($path, array_merge($attrs, ['shortcode' => $this]));
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

<?php

namespace Radiate\Http;

use Illuminate\Support\Traits\Macroable;
use Radiate\View\Factory as ViewFactory;

class ResponseFactory
{
    use Macroable;

    /**
     * The view factory
     *
     * @var \Radiate\View\Factory
     */
    protected $view;

    /**
     * Create the factory instance
     *
     * @param \Radiate\View\Factory $view
     */
    public function __construct(ViewFactory $view)
    {
        $this->view = $view;
    }

    /**
     * Maske a response
     *
     * @param mixed $content
     * @param integer $status
     * @param array $headers
     * @return \Radiate\Http\Response
     */
    public function make($content, int $status, array $headers = []): Response
    {
        return new Response($content, $status, $headers);
    }

    /**
     * Return a view
     *
     * @param string $view
     * @param array $data
     * @param integer $status
     * @param array $headers
     * @return \Radiate\Http\Response
     */
    public function view(string $view, array $data = [], int $status = 200, array $headers = []): Response
    {
        return $this->make($this->view->make($view, $data), $status, $headers);
    }

    /**
     * Return a JSON response
     *
     * @param mixed $content
     * @param integer $status
     * @param array $headers
     * @return \Radiate\Http\Response
     */
    public function json($content = [], int $status = 200, array $headers = [], int $options = 0): Response
    {
        return new JsonResponse($content, $status, $headers, $options);
    }

    /**
     * Redirect responses
     *
     * @param string $location
     * @param integer $status
     * @return \Radiate\Http\Response
     */
    public function redirect(string $location, int $status = 302): Response
    {
        return $this->make('', $status, ['location' => $location]);
    }
}

<?php

namespace Radiate\Support\Facades;

use Radiate\Http\ResponseFactory;

/**
 * @method static \Radiate\Http\Response make(mixed $content, int $status, array $headers = []) Make a response
 * @method static \Radiate\Http\Response view(string $view, array $data = [], int $status = 200, array $headers = []) Return a view
 * @method static \Radiate\Http\JsonResponse json(mixed $content = [], int $status = 200, array $headers = [], int $options = 0) Return a JSON response
 * @method static \Radiate\Http\RedirectResponse redirect(string $location, int $status = 302) Redirect responses
 *
 * @see \Radiate\Http\Response
 */
class Response extends Facade
{
    /**
     * Get the name of the component
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ResponseFactory::class;
    }
}

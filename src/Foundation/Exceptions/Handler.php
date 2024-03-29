<?php

namespace Radiate\Foundation\Exceptions;

use Radiate\Auth\AuthenticationException;
use Radiate\Foundation\Application;
use Radiate\Foundation\Http\Exceptions\HttpExceptionInterface;
use Radiate\Foundation\Http\Exceptions\HttpResponseException;
use Radiate\Http\JsonResponse;
use Radiate\Http\RedirectResponse;
use Radiate\Http\Request;
use Radiate\Http\Response;
use Radiate\Validation\ValidationException;
use Throwable;

class Handler
{
    /**
     * Create the exception handler
     *
     * @param \Radiate\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Render an HTTP exception
     *
     * @param \Radiate\Http\Request $request
     * @param \Throwable $e
     * @return \Radiate\Http\Response
     */
    public function render(Request $request, Throwable $e)
    {
        // run through the variations of exceptions to be thrown and handle them
        // either from the exception itself or within the handler.
        // the response should be a string with headers and status set.
        if ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        } elseif ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($request, $e);
        } elseif ($e instanceof HttpResponseException) {
            return $e->getResponse();
        }

        $headers = $e instanceof HttpExceptionInterface ? $e->getHeaders() : [];

        return $request->expectsJson()
            ? new JsonResponse(['message' => $e->getMessage()], $e->getCode(), $headers)
            : new Response($e->getMessage(), $e->getCode(), $headers);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Radiate\Http\Request  $request
     * @param  \Radiate\Auth\AuthenticationException  $exception
     * @return \Radiate\Http\Response
     */
    protected function unauthenticated(Request $request, AuthenticationException $e): Response
    {
        return $request->expectsJson()
            ? new JsonResponse(['message' => $e->getMessage()], 401)
            : new RedirectResponse($e->redirectTo() ?: wp_login_url());
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Radiate\Http\Request  $request
     * @param  \Radiate\Validation\ValidationException  $e
     * @return \Radiate\Http\Response
     */
    protected function convertValidationExceptionToResponse(Request $request, ValidationException $e): Response
    {
        $data = [
            'message' => $e->getMessage(),
            'errors'  => $e->errors()
        ];

        return $request->expectsJson()
            ? new JsonResponse($data, $e->getCode())
            : new RedirectResponse($this->app->make('url')->previous());
    }
}

<?php

namespace Radiate\Validation;

use Exception;

class ValidationException extends Exception
{
    /**
     * The validator instance.
     *
     * @var \Radiate\Validation\Validator
     */
    public $validator;

    /**
     * The status code to use for the response.
     *
     * @var int
     */
    public $status = 422;

    /**
     * The path the client should be redirected to.
     *
     * @var string
     */
    public $redirectTo;

    /**
     * Create a new exception instance.
     *
     * @param \Radiate\Validation\Validator $validator
     * @return void
     */
    public function __construct($validator)
    {
        parent::__construct('The given data was invalid.');

        $this->validator = $validator;
    }

    /**
     * Get all of the validation error messages.
     *
     * @return array
     */
    public function errors()
    {
        return $this->validator->errors();
    }

    /**
     * Set the HTTP status code to be used for the response.
     *
     * @param  int  $status
     * @return $this
     */
    public function status(int $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Set the URL to redirect to on a validation error.
     *
     * @param string $url
     * @return $this
     */
    public function redirectTo(string $url)
    {
        $this->redirectTo = $url;

        return $this;
    }
}

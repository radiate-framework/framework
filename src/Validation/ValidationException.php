<?php

namespace Radiate\Validation;

use Radiate\Foundation\Http\Exceptions\HttpResponseException;

class ValidationException extends HttpResponseException
{
    /**
     * The validator instance.
     *
     * @var \Radiate\Validation\Validator
     */
    public $validator;

    /**
     * Create a new exception instance.
     *
     * @param \Radiate\Validation\Validator $validator
     * @return void
     */
    public function __construct($validator)
    {
        parent::__construct('The given data was invalid.', 422);

        $this->validator = $validator;
    }

    /**
     * Set the status header and return the message.
     *
     * @return string
     */
    public function getResponse()
    {
        status_header($this->getCode(), $this->getMessage());

        return json_encode(['errors' => $this->validator->errors()]);
    }
}

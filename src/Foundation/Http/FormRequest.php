<?php

namespace Radiate\Foundation\Http;

use Radiate\Foundation\Http\Exceptions\HttpResponseException;
use Radiate\Http\Request;

class FormRequest extends Request
{
    /**
     * Validate the class instance.
     *
     * @return void
     */
    public function validateResolved()
    {
        if (!$this->passesAuthorization()) {
            $this->failedAuthorization();
        }

        $this->validate($this->rules(), $this->messages());
    }

    /**
     * Determine if the request passes the authorization check.
     *
     * @return bool
     */
    protected function passesAuthorization()
    {
        if (method_exists($this, 'authorize')) {
            return $this->authorize();
        }

        return true;
    }

    /**
     * The rules to define on the form request
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * The rules to define on the form request
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Radiate\Foundation\Http\Exceptions\HttpResponseException
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException('Unauthorised.', 401);
    }
}

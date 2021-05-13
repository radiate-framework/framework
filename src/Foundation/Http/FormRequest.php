<?php

namespace Radiate\Foundation\Http;

use Illuminate\Contracts\Container\Container;
use Radiate\Foundation\Http\Exceptions\HttpResponseException;
use Radiate\Http\Request;

class FormRequest extends Request
{
    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

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

        $this->validate($this->container->call([$this, 'rules']), $this->messages());
    }

    /**
     * Determine if the request passes the authorization check.
     *
     * @return bool
     */
    protected function passesAuthorization()
    {
        if (method_exists($this, 'authorize')) {
            return $this->container->call([$this, 'authorize']);
        }

        return true;
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

    /**
     * Set the container implementation.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}

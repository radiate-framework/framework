<?php

namespace Radiate\Validation;

use Closure;
use Illuminate\Contracts\Container\Container;

class Factory
{
    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The Validator resolver instance.
     *
     * @var \Closure
     */
    protected $resolver;

    /**
     * Create a new Validator factory instance.
     *
     * @param  \Illuminate\Contracts\Container\Container|null  $container
     * @return void
     */
    public function __construct(Container $container = null)
    {
        $this->container = $container;
    }

    /**
     * Create a new Validator instance.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @return \Radiate\Validation\Validator
     */
    public function make(array $data, array $rules, array $messages = [])
    {
        $validator = $this->resolve(
            $data,
            $rules,
            $messages
        );

        return $validator;
    }

    /**
     * Validate the given data against the provided rules.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @return array
     */
    public function validate(array $data, array $rules, array $messages = [])
    {
        return $this->make($data, $rules, $messages)->validate();
    }

    /**
     * Resolve a new Validator instance.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @return \Radiate\Validation\Validator
     */
    protected function resolve(array $data, array $rules, array $messages)
    {
        if (is_null($this->resolver)) {
            return new Validator($data, $rules, $messages);
        }

        return call_user_func($this->resolver, $data, $rules, $messages);
    }
}

<?php

namespace Radiate\Validation;

use Closure;
use Illuminate\Contracts\Container\Container;

class Factory
{
    /**
     * Create a new Validator instance.
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return \Radiate\Validation\Validator
     */
    public function make(array $data, array $rules, array $messages = [])
    {
        return $this->resolve($data, $rules, $messages);
    }

    /**
     * Validate the given data against the provided rules.
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return array
     */
    public function validate(array $data, array $rules, array $messages = [])
    {
        return $this->make($data, $rules, $messages)->validate();
    }

    /**
     * Resolve a new Validator instance.
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return \Radiate\Validation\Validator
     */
    protected function resolve(array $data, array $rules, array $messages)
    {
        return new Validator($data, $rules, $messages);
    }
}

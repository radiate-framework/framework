<?php

namespace Radiate\Foundation\Http;

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
        $this->validate($this->rules());
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
}

<?php

namespace Radiate\Validation;

use Closure;

class ClosureRule implements Rule
{
    /**
     * The callback that validates the attribute.
     *
     * @var \Closure
     */
    public $callback;

    /**
     * Indicates if the validation callback failed.
     *
     * @var bool
     */
    public $failed = false;

    /**
     * The validation error message.
     *
     * @var string|null
     */
    public $message;

    /**
     * Create a new Closure based validation rule.
     *
     * @param \Closure $callback
     * @return void
     */
    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes(string $attribute, $value): bool
    {
        $this->failed = false;

        $this->callback->__invoke($attribute, $value, function ($message) {
            $this->failed = true;

            $this->message = $message;
        });

        return !$this->failed;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array|null
     */
    public function message()
    {
        return $this->message;
    }
}

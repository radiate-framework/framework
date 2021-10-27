<?php

namespace Radiate\Validation\Rules;

use Radiate\Support\Facades\Auth;

class CurrentPassword implements Rule
{
    /**
     * The auth guard
     *
     * @var string|null
     */
    protected $guard;

    /**
     * Create the rule
     *
     * @param string|null $guard
     */
    public function __construct(?string $guard = null)
    {
        $this->guard = $guard;
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
        if (!$user = Auth::guard($this->guard)->user()) {
            return false;
        }

        return wp_check_password($value, $user->user_pass, $user->ID);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return 'The password is incorrect';
    }
}

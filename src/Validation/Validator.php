<?php

namespace Radiate\Validation;

use Closure;
use Radiate\Support\Str;
use Radiate\Validation\Rules\Rule;

class Validator
{
    /**
     * The inputs
     *
     * @var array
     */
    protected $inputs = [];

    /**
     * The custom messages
     *
     * @var array
     */
    protected $customMessages = [];

    /**
     * The unparsed rules
     *
     * @var array
     */
    protected $initialRules = [];

    /**
     * The parsed rules
     *
     * @var array
     */
    protected $parsedRules = [];

    /**
     * The error bag
     *
     * @var array
     */
    protected $errorBag = [];

    /**
     * The built in rules
     *
     * @var array
     */
    protected $rules = [
        'accepted'       => Rules\IsAccepted::class,
        'alpha'          => Rules\IsAlpha::class,
        'alpha_dash'     => Rules\IsAlphaDash::class,
        'alpha_num'      => Rules\IsAlphaNum::class,
        'array'          => Rules\IsArray::class,
        'between'        => Rules\IsBetween::class,
        'boolean'        => Rules\IsBoolean::class,
        'date'           => Rules\IsDate::class,
        'digits'         => Rules\IsDigits::class,
        'digits_between' => Rules\IsDigitsBetween::class,
        'email'          => Rules\IsEmail::class,
        'ends_with'      => Rules\EndsWith::class,
        'integer'        => Rules\IsInteger::class,
        'in'             => Rules\IsIn::class,
        'ip'             => Rules\IsIp::class,
        'ipv4'           => Rules\IsIpv4::class,
        'ipv6'           => Rules\IsIpv6::class,
        'max'            => Rules\IsMax::class,
        'min'            => Rules\IsMin::class,
        'numeric'        => Rules\IsNumeric::class,
        'regex'          => Rules\MatchesRegex::class,
        'required'       => Rules\IsRequired::class,
        'starts_with'    => Rules\StartsWith::class,
        'string'         => Rules\IsString::class,
        'size'           => Rules\IsSize::class,
        'timezone'       => Rules\IsTimezone::class,
        'url'            => Rules\IsUrl::class,
    ];

    /**
     * The built in size rules
     *
     * @var array
     */
    protected $sizeRules = [
        Rules\IsBetween::class,
        Rules\IsMax::class,
        Rules\IsMin::class,
        Rules\IsSize::class,
    ];

    /**
     * The built in numeric rules
     *
     * @var array
     */
    protected $numericRules = [
        Rules\IsInteger::class,
        Rules\IsNumeric::class,
    ];

    /**
     * Create the validator instance
     *
     * @param array $inputs
     * @param array $rules
     * @param array $messages
     */
    public function __construct(array $inputs, array $rules, array $messages = [])
    {
        $this->inputs = $inputs;
        $this->initialRules = $rules;
        $this->customMessages = $messages;

        foreach ($rules as $attribute => $rules) {
            $this->parsedRules[$attribute] = $this->parseRules($rules);
        }

        $this->errorBag = [];
    }

    /**
     * Validate the input and return the validated inputs
     *
     * @return array
     */
    public function validate()
    {
        if ($this->fails()) {
            throw new ValidationException($this);
        }

        return $this->validInputs;
    }

    /**
     * Determine if the validation fails
     *
     * @return bool
     */
    public function fails()
    {
        return !$this->passes();
    }

    /**
     * Determine if the validaation passes
     *
     * @return bool
     */
    public function passes()
    {
        foreach ($this->parsedRules as $attribute => $rules) {

            $required = !empty(array_filter($rules, function ($rule) {
                return $rule instanceof Rules\RequiredRule;
            }));

            foreach ($rules as $rule) {

                if (!$rule) {
                    continue;
                }

                if (method_exists($rule, 'setValidator')) {
                    $rule->setValidator($this);
                }

                if ($required) {
                    $this->validateInput($rule, $attribute);
                } elseif ($this->getValue($attribute)) {
                    $this->validateInput($rule, $attribute);
                }
            }
        }

        return !$this->hasErrors();
    }

    /**
     * Validate an input
     *
     * @param \Radiate\Validation\Rules\Rule $rule
     * @param string $attribute
     * @return void
     */
    protected function validateInput(Rule $rule, string $attribute)
    {
        $value = $this->getValue($attribute);

        if (!$rule->passes($attribute, $value)) {
            $this->addErrorMessage($attribute, $rule);
        } else {
            $this->validInputs[$attribute] = $value;
        }
    }

    /**
     * Add an error message to the bag
     *
     * @param string $attribute
     * @param \Radiate\Validation\Rules\Rule $rule
     * @return void
     */
    protected function addErrorMessage(string $attribute, Rule $rule)
    {
        if (!isset($this->errorBag[$attribute])) {
            $this->errorBag[$attribute] = [];
        }

        if ($customMessage = $this->getCustomErrorMessage($rule)) {
            $message = $customMessage;
        } elseif (in_array(get_class($rule), $this->sizeRules)) {
            $message = $this->getSizeMessage($attribute, $rule);
        } else {
            $message = $rule->message();
        }

        $message = $this->replaceAttributePlaceholder($message, $attribute);
        $message = $this->replaceInputPlaceholder($message, $attribute);

        $this->errorBag[$attribute][] = $message;
    }

    /**
     * Get the custom error message if set
     *
     * @param \Radiate\Validation\Rules\Rule $rule
     * @return string|null
     */
    protected function getCustomErrorMessage(Rule $rule)
    {
        $key = array_search(get_class($rule), $this->rules);

        if (isset($this->customMessages[$key])) {
            return $this->customMessages[$key];
        }

        return null;
    }

    /**
     * Get an input value
     *
     * @param string $attribute
     * @return mixed
     */
    protected function getValue(string $attribute)
    {
        return $this->inputs[$attribute];
    }

    /**
     * Replace the :attribute placeholder in the given message.
     *
     * @param string $message
     * @param string $value
     * @return string
     */
    protected function replaceAttributePlaceholder(string $message, string $value)
    {
        $value = $this->getDisplayableAttribute($value);

        return str_replace(
            [':attribute', ':ATTRIBUTE', ':Attribute'],
            [$value, Str::upper($value), Str::ucfirst($value)],
            $message
        );
    }

    /**
     * Replace the :input placeholder in the given message.
     *
     * @param string $message
     * @param string $attribute
     * @return string
     */
    protected function replaceInputPlaceholder(string $message, string $attribute)
    {
        $value = $this->getValue($attribute);

        if (is_scalar($value) || is_null($value)) {
            $message = str_replace([':input', ':value'], $this->getDisplayableValue($value), $message);
        }

        return $message;
    }

    /**
     * Get the displayable attribute.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getDisplayableAttribute(string $attribute)
    {
        return str_replace('_', ' ', Str::snake($attribute));
    }

    /**
     * Get the displayable name of the value.
     *
     * @param mixed $value
     * @return string
     */
    protected function getDisplayableValue($value)
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return $value;
    }

    /**
     * Get the proper error message for an attribute and size rule.
     *
     * @param string $attribute
     * @param \Radiate\Validation\Rules\Rule $rule
     * @return string
     */
    protected function getSizeMessage(string $attribute, Rule $rule)
    {
        $type = $this->getAttributeType($attribute);

        return is_array($message = $rule->message()) ? $message[$type] : $message;
    }

    /**
     * Get the data type of the given attribute.
     *
     * @param string $attribute
     * @return string
     */
    protected function getAttributeType(string $attribute)
    {
        if ($this->hasRule($attribute, $this->numericRules)) {
            return 'numeric';
        } elseif ($this->hasRule($attribute, Rules\IsArray::class)) {
            return 'array';
        }

        return 'string';
    }

    /**
     * Get the size of an attribute.
     *
     * @param string $attribute
     * @param mixed $value
     * @return mixed
     */
    public function getSize(string $attribute, $value)
    {
        $hasNumeric = $this->hasRule($attribute, $this->numericRules);

        if (is_numeric($value) && $hasNumeric) {
            return $value;
        } elseif (is_array($value)) {
            return count($value);
        }

        return mb_strlen($value);
    }

    /**
     * Determine if the given attribute has a rule in the given set.
     *
     * @param string $attribute
     * @param string|array $rules
     * @return bool
     */
    public function hasRule(string $attribute, $rules)
    {
        $matches = array_filter($this->parsedRules[$attribute], function ($rule) use ($rules) {
            return in_array(get_class($rule), (array) $rules);
        });
        return !empty($matches);
    }

    /**
     * Parse the rules
     *
     * @param \Radiate\Validation\Rules\Rule|\Closure|array|string $rules
     * @return array
     */
    protected function parseRules($rules)
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }

        return array_map([$this, 'parseRule'], (array) $rules);
    }

    /**
     * Parse a rule
     *
     * @param \Radiate\Validation\Rules\Rule|\Closure|string $rule
     * @return \Radiate\Validation\Rules\Rule|null
     */
    protected function parseRule($rule)
    {
        if ($rule instanceof Closure) {
            return new ClosureRule($rule);
        }

        if ($rule instanceof Rule) {
            return $rule;
        }

        $parts = explode(':', $rule);

        if (in_array($parts[0], ['regex'])) {
            $parameters = [$parts[1]];
        } else {
            $parameters = $parts[1] ? explode(',', $parts[1]) : [];
        }

        return $this->resolveRule($parts[0], $parameters);
    }

    /**
     * Resolve the rule
     *
     * @param string $rule
     * @param array $parameters
     * @return \Radiate\Validation\Rules\Rule|null
     */
    protected function resolveRule(string $rule, array $parameters)
    {
        if ($rule = $this->rules[$rule]) {
            if (class_exists($rule)) {
                return new $rule(...$parameters);
            }
        }

        return null;
    }

    /**
     * Return the validation errors
     *
     * @return array
     */
    public function errors()
    {
        return $this->errorBag;
    }

    /**
     * Determine if the validator has errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !$this->isValid();
    }

    /**
     * Determine if the validator is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return empty($this->errorBag);
    }
}

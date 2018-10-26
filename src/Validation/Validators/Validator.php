<?php

namespace App\Validation\Validators;

use App\Validation\Rules\RulesSet;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Base validator class.
 */
class Validator
{
    /**
     * Array of rules in standard Laravel format
     *
     * @var array[]|string[]|callable[]|\Illuminate\Validation\Rule[]
     */
    protected $rules = [];

    /**
     * Array of custom validation messages in standard Laravel format
     *
     * @var string[]
     */
    protected $messages = [];

    /**
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * Applies a set of rules and messages to the validator.
     *
     * @param RulesSet $rulesSet
     */
    public function applySet(RulesSet $rulesSet): void
    {
        $this->rules = $rulesSet->getRules();
        $this->messages = $rulesSet->getMessages();
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Adds an array of custom validation messages by merging it with the existing messages.
     *
     * @param array $messages
     */
    public function addOrReplaceMessages(array $messages): void
    {
        $this->messages = array_merge($this->messages, $messages);
    }

    /**
     * Adds a new rule for a given attribute.
     *
     * @param string $attribute
     * @param string|\Illuminate\Validation\Rule|callable $rule
     */
    public function addRule(string $attribute, $rule): void
    {
        if (empty($this->rules[$attribute])) {
            $this->rules[$attribute] = [];
        }
        if (!is_array($this->rules[$attribute])) {
            if (is_string($this->rules[$attribute])) {
                $this->rules[$attribute] = array_map(function ($item) {
                    return $item;
                }, explode('|', $this->rules[$attribute]));
            } else {
                $this->rules[$attribute] = array($this->rules[$attribute]);
            }
        }
        $this->rules[$attribute][] = $rule;
    }

    /**
     * Replaces all rules for the given attribute.
     *
     * @param string $attribute
     * @param string|array|\Illuminate\Validation\Rule|callable $rule
     */
    public function replaceRule(string $attribute, $rule): void
    {
        $this->rules[$attribute] = $rule;
    }

    /**
     * Performs validation of the given data.
     *
     * @param array     $data
     * @param RulesSet  $rulesSet
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validate(array $data, RulesSet $rulesSet = null): \Illuminate\Validation\Validator
    {
        if (!empty($rulesSet)) {
            $this->applySet($rulesSet);
        }
        $this->validator = ValidatorFacade::make($data, $this->rules, $this->messages);

        return $this->validator;
    }

    /**
     * @return bool
     */
    public function fails(): bool
    {
        return $this->validator->fails();
    }

    /**
     * @return bool
     */
    public function passes(): bool
    {
        return $this->validator->passes();
    }

    /**
     * Adds an error for the given attribute.
     *
     * @param string $attribute
     * @param string $message
     */
    public function addError(string $attribute, string $message): void
    {
        $this->validator->errors()->add($attribute, $message);
    }

    /**
     * Returns all errors in a MessageBag or an array of errors for the given attribute.
     *
     * @param string|null $attribute
     *
     * @return \Illuminate\Support\MessageBag | array
     */
    public function getErrors(string $attribute = null)
    {
        return $attribute ? $this->validator->errors()->get($attribute) : $this->validator->errors();
    }

    /**
     * Returns first error for the given attribute.
     *
     * @param string $attribute
     *
     * @return string
     */
    public function getFirstError(string $attribute): string
    {
        return $this->getErrors()->first($attribute);
    }

    /**
     * Is validation fails for the given attribute?
     *
     * @param string $attribute
     *
     * @return bool
     */
    public function hasErrors(string $attribute): bool
    {
        return !empty($this->getErrors($attribute));
    }
}

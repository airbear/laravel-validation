<?php

namespace App\Validation\Rules;

/**
 * Interface for defining sets of rules.
 */
interface RulesSet
{
    /**
     * @return array
     */
    public function getRules(): array;

    /**
     * @return array
     */
    public function getMessages(): array;
}

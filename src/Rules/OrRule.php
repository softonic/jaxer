<?php

namespace Softonic\Jaxer\Rules;

use Softonic\Jaxer\Validation;

class OrRule implements Rule
{
    /**
     * @var Validation[]
     */
    private array $validations;

    public function __construct(Rule ...$rules)
    {
        foreach ($rules as $rule) {
            $this->validations[] = new Validation($rule);
        }
    }

    public function isValid(): bool
    {
        foreach ($this->validations as $validation) {
            if ($validation->isValid()) {
                return true;
            }
        }

        return false;
    }

    public function getContext(): array
    {
        $context = [];

        foreach ($this->validations as $validation) {
            $context[] = $validation->getContext();
        }

        return $context;
    }
}

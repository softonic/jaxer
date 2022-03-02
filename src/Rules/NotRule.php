<?php

namespace Softonic\Jaxer\Rules;

use Softonic\Jaxer\Validation;

class NotRule implements Rule
{
    private Validation $validation;

    public function __construct(private Rule $rule)
    {
        $this->validation = new Validation($rule);
    }

    public function isValid(): bool
    {
        return !$this->validation->isValid();
    }

    public function getContext(): array
    {
        return $this->validation->getContext();
    }
}

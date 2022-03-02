<?php

namespace Softonic\Jaxer;

use Softonic\Jaxer\Rules\Rule;

class Validation
{
    private ?bool $isValid = null;
    private bool $evaluated = false;

    public function __construct(private Rule $rule)
    {
    }

    public function isValid(): bool
    {
        if ($this->evaluated) {
            return $this->isValid;
        }

        $this->isValid   = $this->rule->isValid();
        $this->evaluated = true;

        return $this->isValid;
    }

    public function getContext(): array
    {
        return [
            'rule' => $this->rule::class,
            'context' => $this->rule->getContext(),
            'evaluated' => $this->evaluated,
            'isValid' => $this->isValid,
        ];
    }
}

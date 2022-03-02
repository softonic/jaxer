<?php

namespace Softonic\Jaxer\Rules;

interface Rule
{
    public function isValid(): bool;
    public function getContext(): array;
}

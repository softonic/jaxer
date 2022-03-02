<?php

namespace Softonic\Jaxer\Feature;

use PHPUnit\Framework\TestCase;
use Softonic\Jaxer\Rules\AndRule;
use Softonic\Jaxer\Rules\NotRule;
use Softonic\Jaxer\Rules\OrRule;
use Softonic\Jaxer\Rules\Rule;
use Softonic\Jaxer\Validation;

class ValidationTest extends TestCase
{
    /**
     * @test
     */
    public function whenAddRuleItShouldValidateIt(): void
    {
        $rule = new OrRule(
            new AndRule(
                $this->getRuleTrue(),
                $this->getRuleFalse(),
                $this->getRuleTrue(),
            ),
            new AndRule(
                new NotRule($this->getRuleFalse()),
                $this->getRuleTrue()
            )
        );

        $validation = new Validation($rule);
        $this->assertTrue($validation->isValid());
    }

    private function getRuleTrue(): Rule
    {
        return new class() implements Rule {
            public function isValid(): bool
            {
                return true;
            }

            public function getContext(): array
            {
                return [];
            }
        };
    }

    private function getRuleFalse(): Rule
    {
        return new class() implements Rule {
            public function isValid(): bool
            {
                return false;
            }

            public function getContext(): array
            {
                return [];
            }
        };
    }
}

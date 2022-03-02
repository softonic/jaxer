<?php

namespace Softonic\Jaxer\Rules;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

class AndRuleTest extends TestCase
{
    /**
     * @test
     */
    public function whenAndRuleReceivesOneRuleItShouldValidateIt(): void
    {
        $rule = Mockery::mock(Rule::class);
        $rule->shouldReceive('isValid')
            ->once()
            ->andReturnTrue();
        $rule->shouldReceive('getContext')
            ->once()
            ->andReturn([':key:' => ':value:']);

        $andRule = new AndRule($rule);

        self::assertTrue($andRule->isValid());
        self::assertEquals(
            [
                [
                    'rule' => $rule::class,
                    'context' => [':key:' => ':value:'],
                    'evaluated' => true,
                    'isValid' => true,
                ],
            ],
            $andRule->getContext()
        );
    }

    /**
     * @test
     */
    public function whenAndRuleReceivesMultipleValidRulesItShouldValidateIt(): void
    {
        $rule = Mockery::mock(Rule::class);
        $rule->shouldReceive('isValid')
            ->twice()
            ->andReturnTrue();
        $rule->shouldReceive('getContext')
            ->twice()
            ->andReturn([':key:' => ':value:']);

        $andRule = new AndRule($rule, $rule);

        self::assertTrue($andRule->isValid());
        self::assertEquals(
            [
                [
                    'rule' => $rule::class,
                    'context' => [':key:' => ':value:'],
                    'evaluated' => true,
                    'isValid' => true,
                ],
                [
                    'rule' => $rule::class,
                    'context' => [':key:' => ':value:'],
                    'evaluated' => true,
                    'isValid' => true,
                ],
            ],
            $andRule->getContext()
        );
    }

    /**
     * @test
     */
    public function whenAndRuleReceivesAnEarlyInvalidRuleItShouldStopEarly(): void
    {
        $ruleTrue = Mockery::mock(Rule::class);
        $ruleTrue->shouldNotReceive('isValid');
        $ruleTrue->shouldReceive('getContext')
            ->once()
            ->andReturn([':key:' => ':value:']);

        $ruleFalse = Mockery::mock(Rule::class);
        $ruleFalse->shouldReceive('isValid')
            ->once()
            ->andReturnFalse();
        $ruleFalse->shouldReceive('getContext')
            ->once()
            ->andReturn([':key:' => ':value:']);

        $andRule = new AndRule($ruleFalse, $ruleTrue);

        self::assertFalse($andRule->isValid());
        self::assertEquals(
            [
                [
                    'rule' => $ruleFalse::class,
                    'context' => [':key:' => ':value:'],
                    'evaluated' => true,
                    'isValid' => false,
                ],
                [
                    'rule' => $ruleTrue::class,
                    'context' => [':key:' => ':value:'],
                    'evaluated' => false,
                    'isValid' => null,
                ],
            ],
            $andRule->getContext()
        );
    }
}

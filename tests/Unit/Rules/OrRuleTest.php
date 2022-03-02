<?php

namespace Softonic\Jaxer\Rules;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

class OrRuleTest extends TestCase
{
    /**
     * @test
     */
    public function whenOrRuleReceivesOneRuleItShouldValidateIt(): void
    {
        $rule = Mockery::mock(Rule::class);
        $rule->shouldReceive('isValid')
            ->once()
            ->andReturnTrue();
        $rule->shouldReceive('getContext')
            ->once()
            ->andReturn([':key:' => ':value:']);

        $orRule = new OrRule($rule);

        self::assertTrue($orRule->isValid());
        self::assertEquals(
            [
                [
                    'rule'      => $rule::class,
                    'context'   => [':key:' => ':value:'],
                    'evaluated' => true,
                    'isValid'    => true,
                ],
            ],
            $orRule->getContext()
        );
    }

    /**
     * @test
     */
    public function whenOrRuleReceivesMultipleValidRulesItShouldValidateIt(): void
    {
        $rule = Mockery::mock(Rule::class);
        $rule->shouldReceive('isValid')
            ->once()
            ->andReturnTrue();
        $rule->shouldReceive('getContext')
            ->twice()
            ->andReturn([':key:' => ':value:']);

        $orRule = new OrRule($rule, $rule);

        self::assertTrue($orRule->isValid());
        self::assertEquals(
            [
                [
                    'rule'      => $rule::class,
                    'context'   => [':key:' => ':value:'],
                    'evaluated' => true,
                    'isValid'    => true,
                ],
                [
                    'rule'      => $rule::class,
                    'context'   => [':key:' => ':value:'],
                    'evaluated' => false,
                    'isValid'    => null,
                ],
            ],
            $orRule->getContext()
        );
    }

    /**
     * @test
     */
    public function whenOrRuleReceivesAllInvalidRulesItShouldValidateIt(): void
    {
        $ruleFalse = Mockery::mock(Rule::class);
        $ruleFalse->shouldReceive('isValid')
            ->twice()
            ->andReturnFalse();
        $ruleFalse->shouldReceive('getContext')
            ->twice()
            ->andReturn([':key:' => ':value:']);

        $orRule = new OrRule($ruleFalse, $ruleFalse);

        self::assertFalse($orRule->isValid());
        self::assertEquals(
            [
                [
                    'rule'      => $ruleFalse::class,
                    'context'   => [':key:' => ':value:'],
                    'evaluated' => true,
                    'isValid'    => false,
                ],
                [
                    'rule' => $ruleFalse::class,
                    'context' => [':key:' => ':value:'],
                    'evaluated' => true,
                    'isValid'    => false,
                ],
            ],
            $orRule->getContext()
        );
    }

    /**
     * @test
     */
    public function whenOrRuleReceivesSomeInvalidRulesItShouldValidateIt(): void
    {
        $ruleTrue = Mockery::mock(Rule::class);
        $ruleTrue->shouldReceive('isValid')
            ->once()
            ->andReturnTrue();
        $ruleTrue->shouldReceive('getContext')
            ->once()
            ->andReturn([':key:' => ':value:']);

        $ruleFalse = Mockery::mock(Rule::class);
        $ruleFalse->shouldReceive('isValid')
            ->once()
            ->andReturnFalse();
        $ruleFalse->shouldReceive('getContext')
            ->twice()
            ->andReturn([':key:' => ':value:']);

        $orRule = new OrRule($ruleFalse, $ruleTrue, $ruleFalse);

        self::assertTrue($orRule->isValid());
        self::assertEquals(
            [
                [
                    'rule' => $ruleFalse::class,
                    'context' => [':key:' => ':value:'],
                    'evaluated' => true,
                    'isValid'    => false,
                ],
                [
                    'rule' => $ruleTrue::class,
                    'context' => [':key:' => ':value:'],
                    'evaluated' => true,
                    'isValid'    => true,
                ],
                [
                    'rule' => $ruleFalse::class,
                    'context' => [':key:' => ':value:'],
                    'evaluated' => false,
                    'isValid'    => null,
                ],
            ],
            $orRule->getContext()
        );
    }
}

<?php

namespace Softonic\Jaxer\Rules;

use Generator;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

class NotRuleTest extends TestCase
{
    public function ruleProvider(): Generator
    {
        yield 'truthy rule' => [
            'isValid' => true,
        ];


        yield 'falsy rule' => [
            'isValid' => false,
        ];
    }

    /**
     * @test
     * @dataProvider ruleProvider
     * @param mixed $isValid
     */
    public function whenNotRuleReceivesARuleItShouldInvertTheValidation($isValid): void
    {
        $rule = Mockery::mock(Rule::class);
        $rule->shouldReceive('isValid')
            ->once()
            ->andReturn($isValid);
        $rule->shouldReceive('getContext')
            ->once()
            ->andReturn([':key:' => ':value:']);

        $notRule = new NotRule($rule);

        self::assertEquals(!$isValid, $notRule->isValid());
        self::assertEquals(
            [
                'rule' => $rule::class,
                'context' => [':key:' => ':value:'],
                'evaluated' => true,
                'isValid' => $isValid,
            ],
            $notRule->getContext()
        );
    }
}

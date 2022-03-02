<?php

namespace Softonic\Jaxer;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Softonic\Jaxer\Rules\Rule;

class ValidationTest extends TestCase
{
    /**
     * @test
     */
    public function whenAddRuleItShouldValidateIt(): void
    {
        $rule = Mockery::mock(Rule::class);
        $rule
            ->shouldReceive('isValid')
            ->once()
            ->andReturnTrue();
        $rule
            ->shouldReceive('getContext')
            ->twice()
            ->andReturn([':key:' => ':value:']);

        $validator = new Validation($rule);
        self::assertEquals(
            [
                'rule' => $rule::class,
                'context' => [':key:' => ':value:'],
                'evaluated' => false,
                'isValid'   => null,
            ],
            $validator->getContext()
        );
        self::assertTrue($validator->isValid());
        self::assertEquals(
            [
                'rule' => $rule::class,
                'context' => [':key:' => ':value:'],
                'evaluated' => true,
                'isValid'   => true,
            ],
            $validator->getContext()
        );
    }

    /**
     * @test
     */
    public function whenValidateTwiceARuleItShouldEvaluateItOnce(): void
    {
        $rule = Mockery::mock(Rule::class);
        $rule
            ->shouldReceive('isValid')
            ->once()
            ->andReturnTrue();
        $rule
            ->shouldNotReceive('getContext');

        $validator = new Validation($rule);

        self::assertTrue($validator->isValid());
        self::assertTrue($validator->isValid());
    }
}

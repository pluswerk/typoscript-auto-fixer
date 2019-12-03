<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Fixer\NestedConsistency;

use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\Assignment;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\Identifier;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\Operator;
use PHPUnit\Framework\TestCase;

/**
 * Class AssignmentTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Fixer\NestedConsistency
 * @covers \Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\Assignment
 */
final class AssignmentTest extends TestCase
{
    /**
     * @test
     * @dataProvider assignmentProvider
     */
    public function anAssignmentIsBuiltFromString(string $string, $expectedIdentifier, Operator $expectedOperator, string $expectedValue): void
    {
        $assignment = new Assignment($string);
        $this->assertEquals($expectedIdentifier, $assignment->identifier());
        $this->assertEquals($expectedOperator, $assignment->operator());
        $this->assertEquals($expectedValue, $assignment->value());
    }

    public function assignmentProvider(): array
    {
        $identifier = new Identifier('nest.foo');

        return [
            'equal assignment' => [
                'string' => 'nest.foo = bar',
                'expectedIdentifier' => $identifier,
                'expectedOperator' => Operator::createEqual(),
                'expectedValue' => 'bar'
            ],
            'delete assignment' => [
                'string' => 'nest.foo >',
                'expectedIdentifier' => $identifier,
                'expectedOperator' => Operator::createDelete(),
                'expectedValue' => ''
            ],
            'copy assignment' => [
                'string' => 'nest.foo < bar.path',
                'expectedIdentifier' => $identifier,
                'expectedOperator' => Operator::createCopy(),
                'expectedValue' => 'bar.path'
            ],
            'reference assignment' => [
                'string' => 'nest.foo =< bar.path',
                'expectedIdentifier' => $identifier,
                'expectedOperator' => Operator::createReference(),
                'expectedValue' => 'bar.path'
            ],
            'modification assignment' => [
                'string' => 'nest.foo := appendTo(1,3)',
                'expectedIdentifier' => $identifier,
                'expectedOperator' => Operator::createModification(),
                'expectedValue' => 'appendTo(1,3)'
            ],
            'multi line assignment' => [
                'string' => 'nest.foo (' . PHP_EOL . 'multi' . PHP_EOL . 'line' . PHP_EOL . '  indent' . PHP_EOL . ')' . PHP_EOL,
                'expectedIdentifier' => $identifier,
                'expectedOperator' => Operator::createMultiLine(),
                'expectedValue' => 'multi' . PHP_EOL . 'line' . PHP_EOL . '  indent'
            ]
        ];
    }

    /**
     * @test
     */
    public function assignmentWithoutOperatorThrowsException(): void
    {
        $string = 'assignment.without.operator';
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Assignment needs an operator');
        new Assignment($string);
    }
}
